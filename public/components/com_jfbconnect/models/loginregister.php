<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
jimport('joomla.plugin.helper');
jimport('sourcecoast.utilities');

class JFBConnectModelLoginRegister extends JModel
{
    /**
     * Method to save the form data. Primary implementation from Joomla 1.6 com_users/models/registration.php
     *
     * @param    array        The form data.
     * @return    mixed        The user id on success, false on failure.
     * @since    1.6
     */
    public function register($temp, $useractivation)
    {
        // Initialise the table with JUser.
        JModel::addIncludePath(JPATH_SITE . DS . 'components' . DS . 'com_users' . DS . 'models');
        $userModel = JModel::getInstance('Registration', 'UsersModel');

        $user = new JUser;
        $data = (array)$userModel->getData();

        // Merge in the registration data.
        foreach ($temp as $k => $v)
        {
            $data[$k] = $v;
        }

        // Prepare the data for the user object.
        $data['email'] = $data['email1'];
        $data['password'] = $data['password1'];

        // Check if the user needs to activate their account.
        if (($useractivation == 1) || ($useractivation == 2))
        {
            jimport('joomla.user.helper');
            $data['activation'] = JUtility::getHash(JUserHelper::genRandomPassword());
            $data['block'] = 1;
        }

        // Bind the data.
        if (!$user->bind($data))
        {
            $this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
            return false;
        }

        // Load the users plugin group.
        JPluginHelper::importPlugin('user');

        // Store the data.
        if (!$user->save())
        {
            $this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
            return false;
        }

        return $user;
    }

    function getAutoUsername($fbUser, $fbUserId, $usernamePrefixFormat)
    {
        if ($usernamePrefixFormat == '0') //fb_
        {
            return "fb_" . $fbUserId;
        }
        else if ($usernamePrefixFormat == '1') //first.last
        {
            $firstName = strtolower($fbUser['first_name']);
            $lastName = strtolower($fbUser['last_name']);
            $prefix = $firstName . "." . $lastName;
        }
        else //firlas
        {
            $firstName = strtolower($fbUser['first_name']);
            $lastName = strtolower($fbUser['last_name']);

            $firstPrefix = substr($firstName, 0, 3);
            $lastPrefix = substr($lastName, 0, 3);
            $prefix = $firstPrefix . $lastPrefix;
        }

        $suffix = $this->getUsernameUniqueNumber($prefix);
        return $prefix . $suffix;
    }

    function getUsernameUniqueNumber($prefix)
    {
        $dbo = JFactory::getDBO();
        // First, check if any user has this name
        $query = 'SELECT COUNT(*) FROM #__users WHERE `username` = ' . $dbo->quote($prefix);
        $dbo->setQuery($query);
        $count = $dbo->loadResult();
        if ($count == 0)
            $suffix = "";
        else
        {
            // Get a very strict match to see the last similar username
            $query = 'SELECT CAST(REPLACE(username, ' . $dbo->quote($prefix) . ', "") AS UNSIGNED) suffix FROM #__users WHERE `username` REGEXP "^' . $prefix . '[[:digit:]]+$" ORDER BY `suffix` DESC LIMIT 1';
            $dbo->setQuery($query);
            $suffix = $dbo->loadResult();
            if ($suffix)
            { # increment the last user's number
                $suffix++;
            }
            else
                $suffix = 1;
        }
        return $suffix;
    }

    function &getBlankUser($user, $activationMode)
    {
        jimport('joomla.application.component.helper');
        $config = &JComponentHelper::getParams('com_users');

        
            $usertype = $config->get('new_usertype', 'Registered');
            $acl =& JFactory::getACL();
            $instance = new JUser();
            $instance->set('gid', $acl->get_group_id('', $usertype));
            $instance->set('usertype', $usertype);
        

        

        $instance->set('id', 0);
        $instance->set('name', $user['fullname']);
        $instance->set('username', $user['username']);
        if($user['password'] != "")
            $instance->set('password', $user['password']);
        else
            $instance->set('password_clear', $user['password_clear']);
        $instance->set('email', $user['email']); // Result should contain an email (check)
        $instance->setParam('language', $user['language']);

        if ($activationMode != 0)
        {
            jimport('joomla.user.helper');
            $instance->set('activation', JUtility::getHash(JUserHelper::genRandomPassword()));
            $instance->set('block', 1);
        }
        else
            $instance->set('block', 0);


        if (!$instance->save())
        {
            return JError::raiseWarning('SOME_ERROR_CODE', $instance->getError());
        }

        return $instance;
    }

    public function getLoginRedirect()
    {
        $jfbcLibrary =& JFBConnectFacebookLibrary::getInstance();
        $configModel = $jfbcLibrary->getConfigModel();
        $app =JFactory::getApplication();
        
        if ($jfbcLibrary->initialRegistration && $configModel->getSetting('facebook_new_user_redirect_enable'))
        {
            $itemId = $configModel->getSetting('facebook_new_user_redirect', '0');
            $menu =& $app->getMenu();
            $item =& $menu->getItem($itemId);
            $link = $item->link . "&Itemid=" . $itemId;
            $redirect = JRoute::_($link, false);
        }
        else if ($configModel->getSetting('facebook_login_redirect_enable'))
        {
            $itemId = $configModel->getSetting('facebook_login_redirect', '0');
            $menu =& $app->getMenu();
            $item =& $menu->getItem($itemId);
            $link = $item->link . "&Itemid=" . $itemId;
            $redirect = JRoute::_($link, false);
        }
        else
        {
            //MC $redirect = $this->getNewUserRedirectionURL();
            $redirect = '';            
        }

        return $redirect;
    }

    function getNewUserRedirectionURL()
    {
        $redirect = ''; $menuItemId = 0;
        SCSocialUtilities::getCurrentReturnParameter($redirect, $menuItemId, LOGIN_TASK_JFBCONNECT);
        return $redirect;
    }

}

?>
