<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once "base_facebook.php";

// SourceCoast - Add PHP < 5.1.3 compatibility
if (!function_exists('curl_setopt_array'))
{
    function curl_setopt_array(&$ch, $curl_options)
    {
        foreach ($curl_options as $option => $value)
        {
            if (!curl_setopt($ch, $option, $value))
            {
                return false;
            }
        }
        return true;
    }
}
// End SourceCoast

/**
 * Extends the JFBCBaseFacebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class JFBCFacebook extends JFBCBaseFacebook
{
    var $session = null;

    /**
     * Identical to the parent constructor, except that
     * we start a PHP session to store the user ID and
     * access token if during the course of execution
     * we discover them.
     *
     * @param Array $config the application configuration.
     * @see JFBCBaseFacebook::__construct in facebook.php
     */
    public function __construct($config)
    {
        $this->session = JFactory::getSession();
        parent::__construct($config);
    }

    protected static $kSupportedKeys =
    array('state', 'code', 'access_token', 'user_id');

    /**
     * Provides the implementations of the inherited abstract
     * methods.  The implementation uses PHP sessions to maintain
     * a store for authorization codes, user ids, CSRF states, and
     * access tokens.
     */
    protected function setPersistentData($key, $value)
    {
        if (!in_array($key, self::$kSupportedKeys))
        {
            self::errorLog('Unsupported key passed to setPersistentData.');
            return;
        }


        $session_var_name = $this->constructSessionVariableName($key);
        $this->session->set($session_var_name, $value);
        //$_SESSION[$session_var_name] = $value;
    }

    public function getPersistentData($key, $default = false)
    {
        if (!in_array($key, self::$kSupportedKeys))
        {
            self::errorLog('Unsupported key passed to getPersistentData.');
            return $default;
        }

        $session_var_name = $this->constructSessionVariableName($key);
        return $this->session->get($session_var_name, $default);
        //return isset($_SESSION[$session_var_name]) ?
        //        $_SESSION[$session_var_name] : $default;
    }

    protected function clearPersistentData($key)
    {
        if (!in_array($key, self::$kSupportedKeys))
        {
            self::errorLog('Unsupported key passed to clearPersistentData.');
            return;
        }

        $session_var_name = $this->constructSessionVariableName($key);
        $this->session->clear($session_var_name);
        //unset($_SESSION[$session_var_name]);
    }

    protected function clearAllPersistentData()
    {
        foreach (self::$kSupportedKeys as $key)
        {
            $this->clearPersistentData($key);
        }
    }

    protected function constructSessionVariableName($key)
    {
        return implode('_', array('fb',
                                 $this->getAppId(),
                                 $key));
    }
}
