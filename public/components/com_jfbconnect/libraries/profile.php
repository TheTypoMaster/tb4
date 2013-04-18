<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('sourcecoast.utilities');
jimport('sourcecoast.profile');

class JFBConnectProfileLibrary extends SCProfileLibrary
{
    function __construct(&$subject, $params)
    {
        require_once(JPATH_SITE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php');
        $this->socialLibrary = JFBConnectFacebookLibrary::getInstance();
        $this->configModel = $this->socialLibrary->getConfigModel();
        $this->profilePrefix = 'com_jfbconnect.profiles.';
        $this->profileDirectory = 'jfbcprofiles';

        parent::__construct($subject, $params);
    }

    function jfbcProfilesOnRegister($joomlaId, $fbUserId)
    {
        parent::scProfilesOnRegister($joomlaId);
        self::jfbcProfilesImportAvatar($joomlaId, $fbUserId);
    }

    function jfbcProfilesOnShowRegisterForm()
    {
        return parent::scProfilesOnShowRegisterForm();
    }

    function jfbcProfilesOnFetchData()
    {
        return parent::scProfilesOnFetchData();
    }

    function jfbcProfilesNeedsPermission()
    {
        return false;
    }

    function jfbcProfilesAddFormValidation()
    {
        return parent::scProfilesAddFormValidation();
    }

    function jfbcProfilesOnAuthenticate($jUserId, $fbUserId)
    {
        return parent::scProfilesOnAuthenticate($jUserId, $fbUserId);
    }

    function jfbcProfilesOnLogin($jUserId, $fbUserId)
    {
        //$this->jfbcProfilesOnFetchData();
        parent::scProfilesOnLogin();
        self::jfbcProfilesImportAvatar($jUserId, $fbUserId);
    }

    function jfbcProfilesGetSettings()
    {
        return $this->settings;
    }

    function jfbcProfilesGetPlugins()
    {
        return $this;
    }

    function jfbcProfilesGetRequiredPermissions()
    {
        $fieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . '_field_map');
        $requiredPerms = $this->configModel->getPermissionsForFields($fieldMap);
        return $requiredPerms;
    }

    function getProfilePermissionChoice()
    {
        return true;
    }

    function jfbcProfilesSendsNewUserEmails()
    {
        return false;
    }

    function jfbcProfilesImportProfile($joomlaId, $fbUserId)
    {
        return parent::scProfilesImportProfile($joomlaId, $fbUserId);
    }

    function jfbcProfilesImportAvatar($joomlaId, $fbUserId)
    {
        return parent::scProfilesImportAvatar($joomlaId, $fbUserId);
    }

    /*function getJoomlaUserID()
    {
        $userMapModel = new JFBConnectModelUserMap();
        return $userMapModel->getJoomlaUserId($this->jfbcLibrary->getFbUserId());
    }*/

    function fetchProfile($fields)
    {
        $fbUserId = $this->socialLibrary->getFbUserId();
        $fbProfileFields = $this->socialLibrary->getUserProfile($fbUserId, $fields);
        return $fbProfileFields;
    }

    function getFetchedProfile()
    {
        $session = JFactory::getSession();
        $socialProfileData = $session->get($this->profilePrefix . $this->profileName . '.fetchedData', null);
        $session->clear($this->profilePrefix . $this->profileName . '.fetchedData');
        return $socialProfileData;
    }

    function migrateSocialFieldsToProfile($joomlaId)
    {
        $fbFieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . "_field_map");

        $fbFields = array();
        if (is_array($fbFieldMap))
        {
            foreach ($fbFieldMap as $fbField)
            {
                $fbFieldArray = explode('.', $fbField);
                $fbFields[] = $fbFieldArray[0]; // Get the root field to grab from FB
            }
        }
        if (empty($fbFields))
            return;

        $fbFields = array_unique($fbFields);

        $fbProfileFields = $this->fetchProfile($fbFields);
        if ($fbProfileFields == null)
            return;

        $sql = "";
        foreach ($fbFieldMap as $fieldId => $fbField)
        {
            $parts = explode(".", $fbField);
            $fbData = $fbProfileFields[$parts[0]]; // Get the root field returned from FB for the field we want
            if (count($parts) > 1)
            {
                unset($parts[0]); // Already used this above. Need the selectors only
                foreach ($parts as $part)
                {
                    // First, see if there's an array value we need to choose from
                    $selection = explode(":", $part);
                    if (count($selection) == 2)
                    { // ex: education.type:College.year (Select array with type:College)
                        foreach ($fbData as $option)
                        {
                            if ($option[$selection[0]] == $selection[1])
                                $fbData = $option;
                        }
                    } else // ex: current_location.name (get the name)
                    {
                        if ($fbData != null && array_key_exists($part, $fbData))
                            $fbData = $fbData[$part];
                        else
                            $fbData = null;
                    }
                }
            }

            if ($fbData != null && $fbData != "")
            {
                if (is_array($fbData))
                { // This is a field with multiple, comma separated values
                    // Remove empty values to prevent blah, , blah as output
                    unset($fbData['id']); // Remove id key which is useless to import
                    $fbData = SCStringUtilities::r_implode(', ', $fbData);
                }
                // add custom field handlers here
                switch ($fbField)
                {
                    case 'website':
                        $websites = explode("\n", $fbData);
                        if (count($websites) > 0)
                            $fbData = trim($websites[0]);
                        break;
                }

                if ($fbData) // Don't import blank values
                    $sql .= $this->addFieldToDB($joomlaId, $fieldId, $fbData);
            }
        }

        $this->_db->setQuery($sql);
        $this->_db->queryBatch();
    }

    function getProfileFieldFromArray($fbField, $fbProfileFields)
    {
        $parts = explode(".", $fbField);
        $fbData = $fbProfileFields[$parts[0]]; // Get the root field returned from FB for the field we want
        if (count($parts) > 1)
        {
            unset($parts[0]); // Already used this above. Need the selectors only
            foreach ($parts as $part)
            {
                // First, see if there's an array value we need to choose from
                $selection = explode(":", $part);
                if (count($selection) == 2)
                { // ex: education.type:College.year (Select array with type:College)
                    foreach ($fbData as $option)
                    {
                        if ($option[$selection[0]] == $selection[1])
                            $fbData = $option;
                    }
                } else // ex: current_location.name (get the name)
                {
                    if ($fbData != null && array_key_exists($part, $fbData))
                        $fbData = $fbData[$part];
                    else
                        $fbData = null;
                }
            }
        }

        if ($fbData != null && $fbData != "")
        {
            if (is_array($fbData))
            { // This is a field with multiple, comma separated values
                // Remove empty values to prevent blah, , blah as output
                unset($fbData['id']); // Remove id key which is useless to import
                $fbData = SCStringUtilities::r_implode(', ', $fbData);
            }
            // add custom field handlers here
            switch ($fbField)
            {
                case 'website':
                    $websites = explode("\n", $fbData);
                    if (count($websites) > 0)
                        $fbData = trim($websites[0]);
                    break;
            }
        }

        return $fbData;
    }

    // Created for parity with JLinked/SourceCoast library
    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($fbUserId, $nullForDefault = true)
    {
        $type = 'pic_big'; // Can also do 'pic_big_with_logo'. Need a setting for that one day (Trac #407)
        $fql = "SELECT " . $type . " FROM user WHERE uid = " . $fbUserId;
        $params = array(
            'method' => 'fql.query',
            'query' => $fql,
        );
        $profileUrl = $this->socialLibrary->rest($params, FALSE);
        $avatarUrl = $profileUrl[0][$type];

        //No avatar ends with .gif
        if ($nullForDefault && SCStringUtilities::endswith($avatarUrl, '.gif'))
            $avatarUrl = null;

        return $avatarUrl;
    }

}