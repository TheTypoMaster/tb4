<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Facebook Authentication Plugin
 */
class plgAuthenticationJFBConnectAuth extends JPlugin
{
    var $configModel;

    function plgAuthenticationJFBConnectAuth(& $subject, $config)
    {
         //SC16

        parent::__construct($subject, $config);
    }

    function onAuthenticate($credentials, $options, &$response) //J15
    {
        $this->onUserAuthenticate($credentials, $options, $response);
    }

    function onUserAuthenticate($credentials, $options, &$response)
    {
         //SC16

        # authentication via facebook for Joomla always uses the FB API and secret keys
        # When this is present, the user's FB uid is used to look up their Joomla uid and log that user in
        jimport('joomla.filesystem.file');
        $libraryFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
        if (JFile::exists($libraryFile))
        {
            require_once $libraryFile;
            $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
            $this->configModel = $jfbcLibrary->getConfigModel();
            # always check the secret username and password to indicate this is a JFBConnect login
            #echo "Entering JFBConnectAuth<br>";
            if (($credentials['username'] != $this->configModel->getSetting('facebook_app_id')) ||
                ($credentials['password'] != $this->configModel->getSetting('facebook_secret_key'))
            )
            {
                $response->status = JAUTHENTICATE_STATUS_FAILURE;
                return false;
            }

            #echo "Passed API/Secret key check, this is a FB login<br>";
            include_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');
            $userMapModel = new JFBConnectModelUserMap();

            $fbUserId = $jfbcLibrary->getFbUserId();
            $app = JFactory::getApplication();

            #echo "Facebook user = ".$fbUserId;
            # test if user is logged into Facebook
            if ($fbUserId)
            {
                # Test if user has a Joomla mapping
                $jUserId = $userMapModel->getJoomlaUserId($fbUserId);
                if ($jUserId)
                {
                    $jUser = JUser::getInstance($jUserId);
                    if ($jUser->id == null) // Usermapping is wrong (likely, user was deleted)
                    {
                        $userMapModel->deleteMapping($fbUserId);
                        return false;
                    }

                    if ($jUser->block)
                    {
                        $isAllowed = false;
                         //SC16

                        
                            $app->enqueueMessage(JText::_('E_NOLOGIN_BLOCKED'), 'error');
                         //SC15
                    }
                    else
                    {
                        JPluginHelper::importPlugin('jfbcprofiles');
                        $args = array($jUserId, $fbUserId);
                        $responses = $app->triggerEvent('jfbcProfilesOnAuthenticate', $args);
                        $isAllowed = true;
                        foreach ($responses as $prResponse)
                        {
                            if (is_object($prResponse) && !$prResponse->status)
                            {
                                $isAllowed = false;
                                $app->enqueueMessage($prResponse->message, 'error');
                            }
                        }
                    }

                    if ($isAllowed)
                    {
                        $response->status = JAUTHENTICATE_STATUS_SUCCESS;
                        $response->username = $jUser->username;

                        if (!$this->configModel->getSetting('create_new_users')) # psuedo-users
                        {
                            // Update the J user's email to what it is in Facebook
                            $fbProfileFields = $jfbcLibrary->getUserProfile($fbUserId, array('email'));
                            if ($fbProfileFields != null && $fbProfileFields['email'])
                            {
                                $jUser->email = $fbProfileFields['email'];
                                $jUser->save();
                            }
                        }

                        $response->language = $jUser->getParam('language');
                        $response->email = $jUser->email;
                        $response->fullname = $jUser->name;
                        $response->error_message = '';
                        return true;
                    }
                }

            }
        }

        # catch everything else as an authentication failure
        $response->status = JAUTHENTICATE_STATUS_FAILURE;
        return false;
    }

}
