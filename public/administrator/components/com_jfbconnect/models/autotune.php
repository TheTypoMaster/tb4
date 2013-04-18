<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');


class JFBConnectModelAutoTune extends JModel
{
    var $fieldDescriptors = null;
    private $mergedRecommendations;

    static $displayReplacements = array('blank' => "Value should be blank", 'notblank' => 'Value should not be blank');

    function __construct()
    {
        parent::__construct();
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'config.php');
        $this->configModel = new JFBConnectModelConfig();
    }

    /** Base Functions */
    /*
     * isUpToDate
     * Check when AutoTune was last run and return true if less than 30 days ago, false otherwise
     */
    public function isUpToDate()
    {
        $query = "SELECT updated_at FROM #__jfbconnect_config WHERE `setting` = 'autotune_app_config'";
        $this->_db->setQuery($query);
        $updatedDate = $this->_db->loadResult();
        if ($updatedDate) // not null, check the date
        {
            $updatedTime = strtotime($updatedDate);
            $checkWindow = strtotime('+30 day', $updatedTime);
            if (time() < $checkWindow)
                return true;
        }
        return false;
    }

    public function getSubscriptionStatus()
    {
        $authorization = $this->configModel->getSetting('autotune_authorization', null);
        if ($authorization == null)
        {
            // Make sure the subscriber id is set before trying to fetch info about it
            $subscriptionId = $this->configModel->getSetting('sc_download_id');
            if ($subscriptionId)
                $authorization = $this->makeSourceCoastRequest('getAuthorization');
        }

        if ($authorization) // parse through the messages for better display
        {
            if (isset($authorization->messages) && isset($authorization->messages->expires))
            {
                $expires = $authorization->messages->expires;
                $date = strtotime($expires);
                if ($date < time())
                    $authorization->messages->expires = '<span style="color:#AA3333">' . $expires . ' <strong>(Expired)</strong></span>';
                else
                    $authorization->messages->expires = '<span style="color:#009900">' . $expires . ' (Active)';
            }
        }


        return $authorization;
    }

    public function getVersionURLQuery()
    {
        $instance = new JVersion();
        $jVersion = $instance->getShortVersion();
        // Get JFBConnect version
        $xmlFile = JPATH_ADMINISTRATOR . '/components/com_jfbconnect/jfbconnect.xml';
        if ($xmlParser = simplexml_load_file($xmlFile))
        {
            $jfbcVersion = (string)$xmlParser->version;
        }
        else
            $jfbcVersion = 'unknown'; // should never be the case!

        return 'atVersion=1&jVersion='.$jVersion.'&extVersion='.$jfbcVersion;
    }

    public function makeSourceCoastRequest($task)
    {
        $ch = curl_init();
        $baseUrl = JURI::root();
        $baseUrl = base64_encode(urlencode($baseUrl));

        $app = JFactory::getApplication();
        //$app->enqueueMessage('Making SourceCoast Request: '.$task);

        $subscriptionId = $this->configModel->getSetting('sc_download_id');

        if (defined('JFBCDEV'))
            $url = "http://localhost/autotune/start.php";
        else
            $url = "http://www.sourcecoast.com/autotune/start.php";


        $opts = array();
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_URL] = $url . '?task=jfbconnect.' . $task . '&format=json&'.$this->getVersionURLQuery().'&subscriptionId=' . $subscriptionId . '&baseUrl=' . $baseUrl;
        curl_setopt_array($ch, $opts);
        $json = curl_exec($ch);

        //        $app->enqueueMessage($opts[CURLOPT_URL]);

        $response = json_decode($json);

        if ($response !== null && isset($response->authorization))
        {
            $authorizationObject = $response->authorization;
            $this->configModel->update('autotune_authorization', $authorizationObject);
            if ($authorizationObject->authorized)
            {
                if (isset($response->data))
                    return $response->data;
            }
            else
            {
                if (isset($authorizationObject->error))
                    $app->enqueueMessage($authorizationObject->error, 'error');
                else
                    $app->enqueueMessage('Your Subscriber ID was not associated with an active JFBConnect subscription.', 'error');
            }
        }
        else
            $app->enqueueMessage($task.' - Error: Could not communicate with SourceCoast.com to fetch up-to-date information.', 'error');

        return false;

    }

    /** End Base Functions */

    /*
     * getFieldDescriptors
     * Get the field descriptors for the app configuration. This is originally pulled from SourceCoast.com if never set,
     * but is loaded locally from database by default.
     * If the $forceUpdate value is set, values will be pulled again from SourceCoast
     */
    public function getFieldDescriptors($forceUpdate = false)
    {
        if (!$this->fieldDescriptors)
        {
            // Check if we need to force the update due to stale data
            $updatedAt = $this->configModel->getUpdatedDate('autotune_field_descriptors');
            $checkWindow = strtotime('+30 day', strtotime($updatedAt));

            // Only auto-update settings if inside Autotune
            $controller = strtolower(JRequest::getCmd('view', null));
            if ($controller == 'autotune' && (!$updatedAt || (time() > $checkWindow)))
                $descriptorsOutOfDate = true;
            else
                $descriptorsOutOfDate = false;

            if ($forceUpdate || $descriptorsOutOfDate)
                $this->fetchFieldDescriptors();

            $this->fieldDescriptors = $this->configModel->getSetting('autotune_field_descriptors', null);
        }
        return $this->fieldDescriptors;
    }

    /*
    * fetchFieldDescriptors
    * Called to fetch most recent field descriptors from SourceCoast.com
    */
    private function fetchFieldDescriptors()
    {
        static $updatePerformed;

        if ($updatePerformed)
            return;

        $updatePerformed = true;

        $fields = $this->makeSourceCoastRequest('getFieldList');
        $app = JFactory::getApplication();
        if ($fields)
        {
            $app->enqueueMessage('New fields and recommendations successfully fetched from SourceCoast.com');
            $this->configModel->update('autotune_field_descriptors', $fields);
        }
        else
        {
            $app->enqueueMessage("Could not fetch application and recommendation data from SourceCoast.com. Cached values were loaded, but may be out of date.<br/>
            It's highly recommended that you make a successful connection to get the most up to date application data and recommendations.");
        }
    }

    /*
     * getAppValuesToSave
     * Parses inputs from user against the field descriptor rules to determine what to save, and how it should be saved
     */
    public function getAppValuesToSave($recommendationsOnly = false)
    {
        $fieldDescriptors = $this->getFieldDescriptors();
        $fields = array();
        $appConfig = $this->getAppConfig(false);
        foreach ($fieldDescriptors->group as $group)
        {
            if (strtolower($group->name) == 'migrations')
                continue; //special case, handled separately
            foreach ($group->field as $field)
            {
                if ((isset($field->recommend) && $recommendationsOnly) ||
                        (isset($field->edit) && !$recommendationsOnly)
                )
                {
                    if ($recommendationsOnly)
                    {
                        if ($field->match == 'notblank')
                        {
                            $value = $appConfig[$field->name];
                            $blank = is_array($value) ? count($value) == 0 : $value == "";
                            if (!$blank)
                                continue;
                        }
                        $val = '___';
                    }
                    else
                        $val = JRequest::getVar($field->name, '___');

                    if ($val == '___') // not set, use the recommendation
                        $val = isset($field->recommend) ? $field->recommend : '';

                    if ($field->type == 'array')
                    {
                        $parts = explode(',', $val);
                        $data = array(); //new stdClass();
                        foreach ($parts as $val)
                        {
                            $val = trim($val);
                            $data[] = urlencode($this->replaceAppValues($val, true));
                        }
                        $val = json_encode($data);
                    }
                    else
                    {
                        $val = trim($val);
                        $val = urlencode($this->replaceAppValues($val, true));
                    }
                    $data = new stdClass();
                    $data->name = $field->name;

                    $data->value = $val;
                    $fields[] = $data;
                }
            }
        }
        return $fields;
    }

    public function getAppMigrationsToSave()
    {
        $fieldDescriptors = $this->getFieldDescriptors();
        $migrations = array();
        $fields = array();
        $appConfig = $this->getAppConfig(false);
        $appMigrations = $appConfig['migrations'];
        foreach ($fieldDescriptors->group as $group)
        {
            if (strtolower($group->name) != 'migrations')
                continue;

            foreach ($group->field as $field)
            {
                if (!isset($field->recommend))
                    continue;
                if (array_key_exists($field->name, $appMigrations))
                    $migrations[$field->name] = (int)$this->replaceAppValues($field->recommend, true);
            }
            $field = new stdClass();
            $field->name = 'migrations';
            $field->value = json_encode($migrations);
            $fields[] = $field;
        }
        return $fields;
    }

    /*
     * getMergedRecommendations
     * Parses through the fieldDescriptors and current FB application settings to determine what values should be shown
     * and how. Since not all apps show all values, this function will (appropriately) hide some recommendations from SourceCoast
     */
    public function getMergedRecommendations()
    {
        if (!$this->mergedRecommendations) // Only run this one
        {
            $fieldDescriptors = $this->getFieldDescriptors();
            $appConfig = $this->getAppConfig();
            if (isset($fieldDescriptors->group))
            {
                for ($i = 0; $i < count($fieldDescriptors->group); $i++)
                {
                    $fieldDescriptors->group[$i]->numRecommendations = 0;
                    $group = $fieldDescriptors->group[$i];
                    $fieldLength = count($group->field); // Need to do this since we're unsetting values (no foreach)
                    for ($j = 0; $j < $fieldLength; $j++)
                    {
                        $field = $group->field[$j];
                        if (isset($appConfig[$field->name])) // Returned value is array
                            $value = $appConfig[$field->name];
                        else if (isset($appConfig[strtolower($group->name)])) // Returned value is object
                        {
                            if (isset($appConfig[strtolower($group->name)][$field->name]))
                                $value = $appConfig[strtolower($group->name)][$field->name];
                            else
                            {
                                unset($group->field[$j]);
                                continue; // Only show the migration settings available to the user
                            }
                        }
                        else // Value isn't returned from app, set value to blank
                        {
                            if ($field->type == 'array')
                                $value = array();
                            else
                                $value = "";
                        }

                        if (!is_array($value))
                            $value = $this->replaceAppValues($value);

                        $recMet = true;
                        if (isset($field->match))
                        {
                            // get the 'pretty' display versions of values (Enabled instead of '1', etc)
                            if (isset($field->recommend))
                                $recommend = $this->replaceAppValues($field->recommend);
                            else
                                $recommend = "";

                            $match = isset($field->match) ? $field->match : null;
                            $recMet = $this->checkAppRecommendation($value, $recommend, $match);

                            if (!$recMet)
                                $fieldDescriptors->group[$i]->numRecommendations++;

                            // finally, get the pretty name for the recommendation, if exists
                            if (array_key_exists($match, self::$displayReplacements))
                                $recommend = self::$displayReplacements[$match];

                        }
                        else
                            $recommend = "";

                        $fieldDescriptors->group[$i]->field[$j]->recommend = $recommend;
                        $fieldDescriptors->group[$i]->field[$j]->recommendMet = $recMet;
                        $fieldDescriptors->group[$i]->field[$j]->value = $value;
                    }
                    $this->mergedRecommendations = $fieldDescriptors;
                }
            }
        }
        return $this->mergedRecommendations;
    }

    /*
    * checkAppRecommendation
    */
    private function checkAppRecommendation($value, $recommend, $match)
    {
        switch ($match)
        {
            case "blank" :
                if (is_array($value))
                {
                    if (count($value) > 0)
                        return false;
                    else
                        return true;
                }
                else if ($value != '')
                    return false;
                else
                    return true;
            case 'notblank' :
                if (is_array($value))
                {
                    if (count($value) == 0)
                        return false;
                }
                else if ($value == '')
                    return false;
                else
                    return true;
            case "starts_with":
                if (strpos($value, $recommend) === 0)
                    return true;
            case "exact":
            default: // includes the null case (no match type set)
                if (is_array($value) && in_array($recommend, $value))
                    return true;
                else if (!is_array($value) && $value == $recommend)
                    return true;
                else
                    return false;
        }
    }

    public function getAppConfigField($fieldName)
    {
        $fields = $this->getMergedRecommendations();
        if (isset($fields->group))
        {
            foreach ($fields->group as $group)
            {
                foreach ($group->field as $f)
                {
                    if ($f->name == $fieldName)
                    {
                        $obj = new JObject();
                        $obj->setProperties($f);
                        return $obj;
                    }
                }
            }
        }
        return new JObject();
    }

    /** replaceAppValues()
     * Takes an input value and alters it either for display to the user or to be saved to the FB App
     * $prepareForSave - enable to replace with actual values used to store in FB from the displayReplacements array
     */
    private function replaceAppValues($value, $prepareForSave = false)
    {
        if ($prepareForSave)
        {
            if (array_key_exists($value, self::$displayReplacements))
                $value = ''; // Remove the _BLANK_, etc type tags
            else if ($value == "enabled" || $value == "true")
                $value = 1;
            else if ($value == "disabled" || $value == "false")
                $value = 0;
        }
        else
        {
            if ($value === true || $value === "true" || $value === '1' || $value === 1)
                $value = "Enabled";
            else if ($value === false || $value === "false" || $value === '0' || $value === 0)
                $value = "Disabled";
        }

        return $value;
    }

    public function validateApp($appId, $secretKey)
    {
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $params['access_token'] = $appId . "|" . $secretKey;
        $appConfig = $jfbcLibrary->api($appId, $params, true); // Weird way to set the app key, but necessary since it was just updated
        if ($appConfig)
            return true;
        else
        {
            $app = JFactory::getApplication();
            $app->enqueueMessage('Facebook Application configuration could not be loaded. Please check your App ID and Secret Key', 'error');
            return false;
        }
    }

    public function getAppConfig($forceUpdate = false)
    {
        $appConfig = $this->configModel->getSetting('autotune_app_config', null);
        if ($forceUpdate)
        {

            $fetchFields = $this->getAppFieldsToFetch();
            if (count($fetchFields) > 0)
            {
                //$app = JFactory::getApplication();
                //$app->enqueueMessage('Fetching Facebook Application Information');

                $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
                $appId = $jfbcLibrary->get('facebookAppId', '');
                $appConfig = $jfbcLibrary->api($appId . "?fields=" . implode(',', $fetchFields), null, false);
                if (is_array($appConfig))
                    $this->configModel->update('autotune_app_config', $appConfig);
            }
        }
        // get the App Config from the db instead of what was fetched, in case there was an error
        $appConfig = $this->configModel->getSetting('autotune_app_config');

        return $appConfig;
    }

    public function isNewApp()
    {
        // only do this if the autotune fields have never been entered
        $appConfig = $this->configModel->getSetting('autotune_app_config', null);
        $fields = $this->configModel->getSetting('autotune_field_descriptors', null);
        if ($appConfig != null && $fields != null)
            return false;

        $appFields = $this->getFieldDescriptors(true);
        $appConfig = $this->getAppConfig(true);

        foreach ($appFields->group as $group)
        {
            foreach ($group->field as $field)
            {
                if (isset($field->required) && $field->required == "true")
                {
                    if (!isset($appConfig[$field->name]))
                        return true;
                    else if ($field->type == 'array' && (count($appConfig[$field->name]) == 0))
                        return true;
                    else if ($field->type == 'text' && ($appConfig[$field->name] == ""))
                        return true;
                }
            }
        }
        return false;
    }

    /*
    * getAppFieldsToFetch
    * Generates an array of specific field names to query from Facebook about the application
    */
    private function getAppFieldsToFetch()
    {
        $fields = array();
        $appFields = $this->getFieldDescriptors();
        if (!isset($appFields->version))
            return $fields;

        foreach ($appFields->group as $group)
        {
            if (strtolower($group->name) == "migrations")
                $fields[] = 'migrations';
            else
            {
                foreach ($group->field as $field)
                    $fields[] = $field->name;
            }
        }
        return $fields;
    }

    public function isPluginEnabled($name)
    {
        
        
            $query = 'SELECT published FROM #__plugins WHERE `element`="' . $name . '"';
        
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    public function publishPlugin($name, $status)
    {
        
        
            $query = 'UPDATE #__plugins SET published="' . $status . '" WHERE `type`="plugin" AND `element`="' . $name . '"';
        

        $this->_db->setQuery($query);
        return $this->_db->query();
    }

    public function getJoomlaErrors()
    {
        jimport('joomla.filesystem.file');
        $errors = array();
        // Look for MyAPI
        if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_myapi'))
            $errors[] = 'MyAPI extension detected. MyAPI should be uninstalled as it will conflict with JFBConnect functionality.';

        // Check ordering of System - Cache plugin
        
        
            $query = 'SELECT element, published enabled, ordering FROM #__plugins WHERE `folder`="system" AND (`element`="jfbcsystem" OR `element`="cache") ORDER BY element';
        

        // Order should always come back: cache, jfbcsystem
        $this->_db->setQuery($query);
        $plugins = $this->_db->loadObjectList();
        $cache = $plugins[0];
        $jfbc = $plugins[1];
        if ($cache->enabled && $jfbc->enabled && ($jfbc->ordering >= $cache->ordering))
            $errors[] = 'The "System - Cache" plugin is ordered higher than the JFBCSystem plugin. The "System - Cache" plugin should always be ordered last.';

        // Possible other checks:
        // Gavick social stuff (also could be checked in live)
        // sh404 social stuff (also could be checked in live)
        return $errors;
    }
}