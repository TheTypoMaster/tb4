<?php
/**
 * @package        JLinked
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('sourcecoast.utilities');

class SCProfileLibrary extends JPlugin
{
    var $socialLibrary;
    var $configModel;
    var $profileName;
    var $profilePrefix;
    var $profileDirectory;
    var $settings = array();
    var $_db;
    var $_importEnabled = false; // Can this plugin import previous FB connections
    var $_componentFile = '';
    var $_componentFolder = '';
    var $_componentLoaded = false;

    var $_hasHiddenFields = false;
    var $_settingShowRegistrationFields = null;
    var $_settingShowImportedFields = null;

    function __construct(&$subject, $params)
    {
        $this->profileName = $params['name'];
        $this->_db = JFactory::getDBO();

        $this->_componentLoaded = $this->isComponentInstalled();

        parent::__construct($subject, $params);
    }

    /*     * *
     *
     *  Triggered functions within plugin - These are items that should be called by the dispatcher
     *
     * ** */

    /**
     * Called after registration occurs
     * Good for importing the profile on first registration
     */
    function scProfilesOnRegister($joomlaId)
    {
        return true;
    }

    /**
     * Get field names and inputs to request additional information from users on registration
     * @return string HTML of form fields to display to user on registration
     */
    function scProfilesOnShowRegisterForm()
    {
        $showRegistrationFields = $this->configModel->getSetting($this->_settingShowRegistrationFields);
        $showImportedFields = $this->configModel->getSetting($this->_settingShowImportedFields);
        $html = $this->getRegisterFormFields($showRegistrationFields, $showImportedFields);
        return $html;
    }

    /**
     * Determine if the Login Register view needs to give user the option to approve profile import
     * @return bool if permission is needed, false if not
     */
    function scProfilesNeedsPermission()
    {
        return $this->_hasHiddenFields;
    }

    /**
     * Profile will add its form validation script. If no custom validation is required,
     * default validation will be performed
     * @return bool
     */
    function scProfilesAddFormValidation()
    {
        return false;
    }

    /**
     * Used for plugins to check any credentials or information as necessary
     * Return true if login should proceed, false if not
     */
    function scProfilesOnAuthenticate($jUserId, $liMemberId)
    {
        $response = new profileResponse();
        $response->status = true;
        return $response;
    }

    /**
     * Triggered after user is successfully logged into the site. Good for importing profile, updating status, etc
     */
    function scProfilesOnLogin()
    {
        return true;
    }

    function scProfilesGetSettings()
    {
        return $this->settings;
    }

    function scProfilesGetPlugins()
    {
        return $this;
    }

    function scProfilesGetRequiredPermissions()
    {
        $fieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . '_field_map');
        $requiredPerms = $this->configModel->getPermissionsForFields($fieldMap);
        return $requiredPerms;
    }

    function scProfilesSendsNewUserEmails()
    {
        return false;
    }

    function scProfilesOnFetchData()
    {
        $socialFieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . "_field_map");

        if($socialFieldMap == "")
            return;

        $fields = array();
        foreach ($socialFieldMap as $socialField)
        {
            // Strip out any path information from the field
            $loc = strpos($socialField, '.');
            if ($loc)
                $socialField = substr($socialField, 0, $loc);
            $fields[] = $socialField;
        }

        $fields = array_unique($fields);

        $socialProfile = $this->fetchProfile($fields);

        $session = JFactory::getSession();
        if(is_array($socialProfile))
            $profileData = $socialProfile;
        else if($socialProfile)
            $profileData = $socialProfile->data;
        else
            $profileData = null;

        if($profileData)
            $session->set($this->profilePrefix.$this->profileName.'.fetchedData', $profileData);
    }

    function scProfilesImportProfile($joomlaId, $socialUserId)
    {
        if (!$this->isComponentInstalled())
            return;

        // Determine if we should re-import the avatar
        if ($this->socialLibrary->initialRegistration || $this->configModel->getSetting('profiles_' . $this->profileName . "_import_always"))
        {
            $this->migrateSocialFieldsToProfile($joomlaId);

            //Copy over the FB Avatar to CB...
            if ($this->configModel->getSetting('profiles_' . $this->profileName . "_import_avatar"))
                $this->migrateSocialAvatarToProfile($joomlaId, $socialUserId);
        }
    }

    function scProfilesImportAvatar($joomlaId, $socialUserId)
    {
        if (!$this->isComponentInstalled())
            return;

        // Determine if we should re-import the avatar
        if ($this->socialLibrary->initialRegistration || $this->configModel->getSetting('profiles_' . $this->profileName . "_import_always"))
        {
            //Copy over the FB Avatar to CB...
            if ($this->configModel->getSetting('profiles_' . $this->profileName . "_import_avatar"))
                $this->migrateSocialAvatarToProfile($joomlaId, $socialUserId);
        }
    }

    /*     * *
     *
     * ************ END Triggered functions ************
     *
     * ** */

    /*     * *
     * ************ Direct call functions **************
     */

    function getConfigurationTemplate()
    {
        
        $file = JPATH_SITE . DS . 'plugins' . DS . $this->profileDirectory . DS . $this->profileName . DS . 'tmpl' . DS . 'configuration.php';
         //SC15
         //SC16

        if (!JFile::exists($file))
            return "No configuration is required for this profile plugin";

        $this->profileFields = $this->getProfileFields();
        ob_start();
        include_once($file);
        $config = ob_get_clean();
        return $config;
    }

    function getName()
    {
        return $this->profileName;
    }

    /*     * ***
     * ************* END Direct call functions ********8
     */

    function getProfileFields()
    {
        return array();
    }

    function getFetchedProfile()
    {
        return array();
    }

    /* Method to retrieve HTML for registration fields. To be used when 3rd party extension
     * does not have custom fields
     */

    function getRegisterFormFields($showRegistrationFields, $showImportedFields)
    {
        $socialFieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . "_field_map");
        if (!is_array($socialFieldMap))
            $socialFieldMap = array();

        $html = "";

        $session = JFactory::getSession();
        $postData = $session->get('postDataLoginRegister', array());

        $socialProfile = $this->getFetchedProfile();
        $profileFields = $this->getProfileFields();

        foreach ($profileFields as $profileFieldName=>$profileFieldLabel)
        {
            $isMapped = is_array($socialFieldMap) && array_key_exists($profileFieldName, $socialFieldMap);
            $showHiddenField = $this->shouldBeHiddenField($showRegistrationFields, $showImportedFields, $isMapped, true);
            $showVisibleField = $showRegistrationFields == "1" &&
                    ($showImportedFields == "1" || ($showImportedFields == "0" && !$isMapped));

            $fieldValue = '';
            if(array_key_exists($profileFieldName, $postData))
            {
                $fieldValue = $postData[$profileFieldName];
            }
            else if($isMapped && $socialProfile)
            {
                $fieldName = $socialFieldMap[$profileFieldName];
                if(is_array($socialProfile))
                    $fieldValue = $this->getProfileFieldFromArray($fieldName, $socialProfile);
                else
                    $fieldValue = $socialProfile->get($fieldName);
            }

            if($showHiddenField)
            {
                $this->_hasHiddenFields = true;
                $html .= '<input type="hidden" name="'.$profileFieldName.'" id="'.$profileFieldName.'" value="'.$fieldValue.'" />';
            }
            else if($showVisibleField)
            {
                $html .= '<label for="'.$profileFieldName.'">'.$profileFieldLabel.'</label>';
                $html .= '<input type="text" name="'.$profileFieldName.'" id="'.$profileFieldName.'" value="'.$fieldValue.'" /><br/>';
            }
        }

        return $html;
    }

    function getProfileFieldFromArray($fieldName, $socialProfile)
    {
        return "";
    }

    function getProfilePermissionChoice()
    {
        return false;
    }

    function resetFormFieldData($joomlaId, $fbUserId)
    {
        // Reset fields if needed from post
        if($this->configModel->getSetting('create_new_users') || JRequest::getString('option') == 'com_jlinked')
        {
            $showRegistrationFields = $this->configModel->getSetting($this->_settingShowRegistrationFields);
            $showImportedFields = $this->configModel->getSetting($this->_settingShowImportedFields);

            $socialFieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . "_field_map");
            if (!is_array($socialFieldMap))
                $socialFieldMap = array();

            $profileFields = $this->getProfileFields();

            $sql = "";
            foreach($profileFields as $profileFieldName=>$profileFieldLabel)
            {
                $isMapped = is_array($socialFieldMap) && array_key_exists($profileFieldName, $socialFieldMap);
                $showHiddenField = $this->shouldBeHiddenField($showRegistrationFields, $showImportedFields, $isMapped, true);
                $clearOutValue = $showHiddenField && !$this->getProfilePermissionChoice();

                $formValue = JRequest::getVar($profileFieldName, null, 'POST');
                if($clearOutValue)
                    $formValue = '';

                $sql .= $this->addFieldToDB($joomlaId, $profileFieldName, $formValue);
            }

            if($sql != "")
            {
                $this->_db->setQuery($sql);
                $this->_db->queryBatch();
            }
        }
        else
        {
            $this->scProfilesImportProfile($joomlaId, $fbUserId);
        }
    }

    function shouldBeHiddenField($showRegistrationFields, $showImportedFields, $isMapped, $isRequired)
    {
        $showHiddenField = ($showRegistrationFields == '0') ||
                            ($showImportedFields == "0" && $isMapped) ||
                            ($showRegistrationFields == '1' && $showImportedFields == '1' && $isMapped && !$isRequired);

        return $showHiddenField;
    }

    function migrateSocialFieldsToProfile($joomlaId)
    {
        $socialFieldMap = $this->configModel->getSetting('profiles_' . $this->profileName . "_field_map");

        $fields = array();
        foreach ($socialFieldMap as $socialField)
        {
            // Strip out any path information from the field
            $loc = strpos($socialField, '.');
            if ($loc)
                $socialField = substr($socialField, 0, $loc);
            $fields[] = $socialField;
        }

        $fields = array_unique($fields);

        $socialProfile = $this->fetchProfile($fields);
        $sql = "";
        if($socialProfile)
        {
            foreach ($socialFieldMap as $fieldId => $socialField)
            {
                $value = $socialProfile->get($socialField);

                if ($value != null && $value != "")
                {
                    if (is_array($value))
                    { // This is a field with multiple, comma separated values
                        // Remove empty values to prevent blah, , blah as output
                        unset($value['id']); // Remove id key which is useless to import
                        $value = SCStringUtilities::r_implode(', ', $value);
                    }
                    $sql .= $this->addFieldToDB($joomlaId, $fieldId, $value);
                }
            }
        }

        $this->_db->setQuery($sql);
        $this->_db->queryBatch();
    }

    function migrateSocialAvatarToProfile($joomlaId, $socialUserId)
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.utilities.utility');

        $avatarURL = $this->getAvatarURL($socialUserId, true);
        if ($avatarURL == null)
        {
            $this->setDefaultAvatar($joomlaId);
            return false;
        }

        $data = SCSocialUtilities::getRemoteContent($avatarURL);
        if ($data)
        {
            $baseImgPath = $this->getAvatarPath();
            $tmpImgName = 'scprofile_' . $joomlaId . '_pic_tmp.jpg';
            JFile::write($baseImgPath . DS . $tmpImgName, $data);
            if ($this->updateAvatar($tmpImgName, $joomlaId, $socialUserId))
                return true;
        }

        # there was a problem adding the avatar, use the default
        $this->setDefaultAvatar($joomlaId);
        return false;

    }

    function addFieldToDB($joomlaId, $fieldId, $value)
    {
        return '';
    }

    function getAvatarPath()
    {
        $app = JFactory::getApplication();
        $tmpPath = $app->getCfg('tmp_path');
        return $tmpPath;
    }

    function setDefaultAvatar($userId)
    {
        return true;
    }

    function updateAvatar($fbAvatar, $userId, $liUserId)
    {
        return true;
    }

    function canImportConnections()
    {
        return $this->_importEnabled;
    }

    function isComponentInstalled()
    {
        $componentDetected = false;
        jimport('joomla.filesystem.file');
        if ($this->_componentFile != '')
        {
            $componentDetected = JFile::exists($this->_componentFolder . DS . $this->_componentFile);
        }
        else if ($this->_componentFolder != '')
        {
            $componentDetected = JFolder::exists($this->_componentFolder);
        }

        return $componentDetected;
    }
}

if (!class_exists('profileResponse'))
{
    class profileResponse
    {

        var $status;
        var $message;

    }
}