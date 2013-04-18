<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('sourcecoast.utilities');

class JFBConnectController extends JController
{

    function display($cachable = false, $urlparams = false)
    {
        parent::display();
    }

    function loginFacebookUser()
    {
        // Set a cookie to prevent auto-logging in for the remainder of the session time
        $config = JFactory::getConfig();
        $lifetime = $config->get('lifetime', 15);
        setcookie('jfbconnect_autologin_disable', 1, time() + ($lifetime * 60));
        // Not a perfect solution, but fixes autologin loops..

        $app = JFactory::getApplication();
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $fbUserId = $jfbcLibrary->getFbUserId();

        require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');
        $userMapModel = new JFBConnectModelUserMap();

        $configModel = $jfbcLibrary->getConfigModel();

        $user = JFactory::getUser();
        if ($user->guest)
        { # Guest: Check if they have a Joomla user and log that user in. If not, create them one
            $jUserId = $userMapModel->getJoomlaUserId($fbUserId);

            if (!$fbUserId)
            { # Facebook isn't returning information about this user.  Redirect them.
                $app->enqueueMessage('We were unable to retrieve your Facebook account information. Please try again');
                $app->redirect('index.php');
            }

            # Check if automatic email mapping is allowed, and see if that email is registered
            # AND the Facebook user doesn't already have a Joomla account
            if ($configModel->getSetting('facebook_auto_map_by_email') && !$jUserId)
            {
                $fbProfileFields = $jfbcLibrary->getUserProfile($fbUserId, array('email'));
                if ($fbProfileFields != null && $fbProfileFields['email'])
                {
                    $fbEmail = $fbProfileFields['email'];
                    $jUserEmailId = $userMapModel->getJoomlaUserIdFromEmail($fbEmail);
                    if ($jUserEmailId && $jUserEmailId != 0)
                    {
                        // Found a user with the same email address
                        // do final check to make sure there isn't a FB account already mapped to it
                        $jUserId = $userMapModel->getFacebookUserId($jUserEmailId);
                        if (!$jUserId)
                        {
                            SCSocialUtilities::clearJFBCNewMappingEnabled();
                            if ($userMapModel->mapUser($fbUserId, $jUserEmailId))
                                $app->enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_SUCCESS'));
                            else
                                $app->enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_FAIL'));

                            $jUserId = $jUserEmailId; // Update the temp jId so that we login below
                        }
                    }
                }
            }

            if ($configModel->getSetting('create_new_users') && $jUserId == null)
            { # User not in system, create new users setting enabled. Redirect to the login/register form

                SCSocialUtilities::setJFBCNewMappingEnabled();
                $redirect = '';
                $menuItemId = 0;
                SCSocialUtilities::getCurrentReturnParameter($redirect, $menuItemId, LOGIN_TASK_JFBCONNECT);
                $app->redirect(JRoute::_('index.php?option=com_jfbconnect&view=loginregister&return=' . base64_encode($redirect), false));
            }

            #set the initial registration variable so that the user plugin knows to map user and trigger onAfterRegistration
            if ($jUserId == null)
                $jfbcLibrary->setInitialRegistration();

            require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'loginregister.php');
            $loginController = new JFBConnectControllerLoginRegister();
            $loginController->login(); // Perform the login function
        } else # Already logged into Joomla. Update their facebook mapping
        {
            SCSocialUtilities::clearJFBCNewMappingEnabled();
            if ($userMapModel->mapUser($fbUserId))
                $app->enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_SUCCESS'));
            else
                $app->enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_FAIL'));
        }

        $loginRegisterModel = $this->getModel('LoginRegister', 'JFBConnectModel');
        $return = $loginRegisterModel->getLoginRedirect();
        $app->redirect($return);
    }

    function logout()
    {
        // Setup the logout settings
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $configModel = $jfbcLibrary->getConfigModel();
        if ($configModel->getSetting('facebook_logout_redirect_enable'))
        {
            $logoutItemId = $configModel->getSetting('facebook_logout_redirect', 'index.php');
            $return = JRoute::_("index.php?Itemid=" . $logoutItemId);
            $menuItemId = $logoutItemId;
        } else
        {
            $return = '';
            $menuItemId = 0;
            SCSocialUtilities::getCurrentReturnParameter($return, $menuItemId, LOGIN_TASK_JFBCONNECT);
        }

        // Tell the FB PHP Library to clear the session and cookies from FB for this user
        // Generally, the JS library takes care of the cookie deletion, and JFBConnect/Joomla take care of the state
        // But, good to call their function for when they introduce bugs.. like we've seen before.
        $logoutJoomlaOnly = $configModel->getSetting('logout_joomla_only');
        if (!$logoutJoomlaOnly)
        {
            $fbClient = $jfbcLibrary->getFbClient();
            $fbClient->destroySession();
        }

        // If the logout Link is registered, redirect to the homepage instead
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__menu WHERE id=" . $db->quote($menuItemId);
        $db->setQuery($query);
        $menuItem = $db->loadObject();

        
            $publicAccessValue = "0";
         //SC15

         //SC16

        if ($menuItem && $menuItem->access != $publicAccessValue)
            $return = 'index.php';

        $app = JFactory::getApplication();
        $app->logout();

        $app->redirect($return);
    }

    function deauthorizeUser()
    {
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $fbClient = $jfbcLibrary->getFbClient();

        $signedRequest = JRequest::getString('signed_request', null, 'POST');
        if ($signedRequest)
        {
            $parsed = $fbClient->parseSignedRequest($signedRequest);
            $fbUserId = $parsed['user_id'];
            if ($fbUserId)
            {
                JModel::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models');
                $userModel = JModel::getInstance('UserMap', 'JFBConnectModel');
                $userModel->setAuthorized($fbUserId, '0');
            }
        }
        exit;
    }

/*  Not ready for primetime yet. The setInitialRegistration causes issues.
    function updateProfile()
    {
        $jUser = JFactory::getUser();
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $jfbcLibrary->setInitialRegistration();
        $fbUserId = $jfbcLibrary->getMappedFbUserId();
        $args = array($jUser->get('id'), $fbUserId);

        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('jfbcprofiles');
        $app->triggerEvent('scProfilesImportProfile', $args);
        $app->enqueueMessage('Profile Imported!');
        $app->redirect('index.php');
    }*/
}
