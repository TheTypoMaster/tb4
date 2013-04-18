<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JFBCConfig extends JModel
{
    var $_profilePlugin;
    var $_profileSettings;
    var $_profileGetPlugin;

    function __construct()
    {
        parent::__construct();
    }

    function getAvailableSettings()
    {
        # Get all possible settings from add-ons
        $this->_availableSettings = $this->componentSettings;
        JPluginHelper::importPlugin($this->_profilePlugin);
        $app = JFactory::getApplication();
        $settings = $app->triggerEvent($this->_profileSettings);
        foreach ($settings as $settingArray)
            $this->_availableSettings = array_merge($this->_availableSettings, $settingArray);
    }

    function store()
    {
        $row = &$this->getTable();
        $data = JRequest::get('post');
        if (!$row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        $row->updated_at = JFactory::getDate()->toMySQL();
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    function update($setting, $value)
    {
        if(is_array($value) || is_object($value))
            $value = serialize($value);
        else
            $value = trim($value);
        $query = "SELECT id FROM ".$this->table.
                " WHERE `setting`=" . $this->_db->quote($setting);
        $this->_db->setQuery($query);
        $settingId = $this->_db->loadResult();

        $row = $this->getTable();
        $row->id = $settingId;
        $row->setting = $setting;
        $row->value = $value;
        if (!$settingId)
            $row->created_at = JFactory::getDate()->toMySQL();
        $row->updated_at = JFactory::getDate()->toMySQL();
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (self::$_settings == null)
        {
            $this->getSettings();
        }
        self::$_settings[$setting]->value = $value;

        return true;
    }

    static $_settings = null;
    function getSettings()
    {
        if (self::$_settings == null)
        {
            $query = "SELECT setting,value FROM ".$this->table;
            $this->_db->setQuery($query);
            self::$_settings = $this->_db->loadObjectList('setting');
        }
    }

    function getSetting($setting, $default = '')
    {
        if (self::$_settings == null)
        {
            $this->getSettings();
        }

        if (array_key_exists($setting, self::$_settings))
        {
            $value = self::$_settings[$setting]->value;
        }
        else # load default value
        {
            // Do a quick check to see if it's a component setting, and get it's default
            if (array_key_exists($setting, $this->componentSettings))
                $value = $this->componentSettings[$setting];
            else
            {
                $this->getAvailableSettings();
                $value = $this->_availableSettings[$setting];
            }
        }

        if (strpos($setting, "_field_map") || (strpos($setting, "autotune_") !== false))
            $value = @unserialize($value); // Suppress the notice that the string may not be serialized in the first place

        if ($value === null || $value === '' || $value === false)
        {
            $value = $default;
        }

        return $value;
    }


    function getAvailableProfileFields()
    {
        return $this->profileFields;
    }

    function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $row = & $this->getTable();
        if (count($cids))
        {
            foreach ($cids as $cid)
            {
                if (!$row->delete($cid))
                {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    /*
     * $configs = Array (POST) of setting->value pairs.
     * Checked against the availableSettings array for valid settings
     */

    function saveSettings($configs)
    {
        $this->getAvailableSettings();
        $settings = array_intersect_key($configs, $this->_availableSettings);
        foreach ($settings as $setting => $value)
        {
            $this->update($setting, $value);
        }

        $app = JFactory::getApplication();
        JPluginHelper::importPlugin($this->_profilePlugin);
        $profiles = $app->triggerEvent($this->_profileGetPlugin);
        foreach ($profiles as $profile)
        {
            # Look for any field_map profile settings available
            $fieldMapName = "profiles_" . $profile->getName() . "_field_map";
            $fieldLength = strlen($fieldMapName);
            $fbFields = array();
            $fieldMapFound = false; // Only update the fieldMap column if one of the settings is intended for it
            foreach ($configs as $key => $value)
            {
                if (substr($key, 0, $fieldLength) == $fieldMapName)
                {
                    $fieldMapFound = true;
                    $newKey = str_replace($fieldMapName, "", $key);
                    if ($value != "0")
                        $fbFields[$newKey] = $value;
                }
            }
            if ($fieldMapFound)
                $this->update($fieldMapName, serialize($fbFields));
        }
        self::$_settings = null; // Clear all the settings so they're reloaded next time any are needed
    }

    function getUpdatedDate($field)
    {
        $query = 'SELECT updated_at FROM #__jfbconnect_config WHERE setting = "'.$field.'"';
        $this->_db->setQuery($query);
        $date = $this->_db->loadResult();
        return $date;
    }

}
