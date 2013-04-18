<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JFBConnectControllerAutoTune extends JFBConnectController
{
    function __construct()
    {
        JRequest::setVar('view', 'AutoTune');
        $libFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
        require_once($libFile);

        parent::__construct();
    }

    function display($cachable = false, $urlparams = false)
    {
        $task = JRequest::getCmd('task', 'default');

        if ($task != 'default' && $task != 'basicinfo')
            $this->checkBasicInfo();

        $viewLayout = $task;
        $autotuneModel = $this->getModel('autotune');
        $this->view->setModel($autotuneModel, true);

        $configModel = $this->getModel('config');
        $this->view->setModel($configModel, false);

        switch ($task)
        {
            case 'basicinfo':
                JToolBarHelper::custom('display', 'back', 'back', "Start", false);
                JToolBarHelper::custom('saveBasicInfo', 'forward', 'forward', "FB App", false);
                $this->getBasicInfo();
                break;
            case 'fbappRefresh':
            case 'fbapp':
                $viewLayout = 'fbapp';
                // Check if we should redirect to the new app page
                if ($autotuneModel->isNewApp())
                {
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbappnew');
                    $this->redirect();
                }

                $fields = $autotuneModel->getFieldDescriptors(false);
//                if ($fields == null) // Check if we should force load the data
//                    $autotuneModel->getFieldDescriptors(true);
                $appConfig = $autotuneModel->getAppConfig(false);
//                if ($appConfig == null)
//                    $autotuneModel->getFieldDescriptors(true);

                JToolBarHelper::custom('fbappRefresh', 'refresh', 'refresh', 'Refresh', false, false);
                JToolBarHelper::divider();
                JToolBarHelper::custom('basicinfo', 'back', 'back', 'Basic Info', false);
                JToolBarHelper::custom('siteconfig', 'forward', 'forward', 'Site Config', false);
                break;
            case 'fbappnew':
                break;
            case 'siteconfig':
                JToolBarHelper::custom('fbapp', 'back', 'back', 'FB App', false);
                JToolBarHelper::custom('errors', 'forward', 'forward', 'Error Check', false);
                $this->setupSiteConfig();
                break;
            case 'errors':
                JToolBarHelper::custom('siteconfig', 'back', 'back', 'Site Config', false);
                JToolBarHelper::custom('finish', 'forward', 'forward', 'Finish', false);
                $this->setupCheckErrors();
                break;
            case 'finish':
                JToolBarHelper::custom('errors', 'back', 'back', 'Error Check', false);
                break;
            case 'default':
            default:
                $this->setupIntroPage();
                JToolBarHelper::custom('fbapp', 'forward', 'forward', 'FB App', false);
                break;
        }
        JToolBarHelper::divider();
        JToolBarHelper::cancel('exitAutoTune', 'Exit AutoTune');
        $this->view->setLayout($viewLayout);
        $this->view->display();
    }

    public function exitAutoTune()
    {
        $this->setRedirect('index.php?option=com_jfbconnect');
        $this->redirect();
    }

    public function fbappRefresh()
    {
        $autotuneModel = $this->getModel('autotune');
        $autotuneModel->getFieldDescriptors(true);
        $autotuneModel->getAppConfig(true);
        $this->display();
    }

    private function setupSiteConfig()
    {
        $autotuneModel = $this->getModel('autotune');
        $JFBCSystemEnabled = $autotuneModel->isPluginEnabled('jfbcsystem');
        $JFBCAuthenticationEnabled = $autotuneModel->isPluginEnabled('jfbconnectauth');
        $JFBCContentEnabled = $autotuneModel->isPluginEnabled('jfbccontent');
        $JFBCUserEnabled = $autotuneModel->isPluginEnabled('jfbconnectuser');

        $errors = $autotuneModel->getJoomlaErrors();

        $this->view->assignRef('JFBCSystemEnabled', $JFBCSystemEnabled);
        $this->view->assignRef('JFBCAuthenticationEnabled', $JFBCAuthenticationEnabled);
        $this->view->assignRef('JFBCContentEnabled', $JFBCContentEnabled);
        $this->view->assignRef('JFBCUserEnabled', $JFBCUserEnabled);
        $this->view->assignRef('joomlaErrors', $errors);
    }

    private function setupIntroPage()
    {
        $phpVersion = phpversion();
        $errorsFound = false;
        if (version_compare($phpVersion, '5.0.0') >= 0)
            $phpVersion .= '<td><img src="components/com_jfbconnect/assets/images/icon-16-allow.png" /></td>';
        else
        {
            $phpVersion .= '<td><img src="components/com_jfbconnect/assets/images/icon-16-deny.png" /></td>';
            $errorsFound = true;
        }
        $this->view->assignRef('phpVersion', $phpVersion);

        // cURL check
        $disableFunctions = ini_get('disable_functions');
        if (in_array('curl', get_loaded_extensions()) && strpos($disableFunctions, 'curl_exec') === false)
            $curlCheck = 'Enabled <td><img src="components/com_jfbconnect/assets/images/icon-16-allow.png" /></td>';
        else
        {
            $curlCheck = '<strong>Disabled</strong> <td><img src="components/com_jfbconnect/assets/images/icon-16-deny.png" /></td>';
            $errorsFound = true;
        }

        if ($errorsFound)
        {
            $app = JFactory::getApplication();
            $app->enqueueMessage('Server Configuration Errors Found! Please correct the errors listed before continuing.', 'error');
        }
        $this->view->assignRef('curlCheck', $curlCheck);
        $this->view->assignRef('errorsFound', $errorsFound);
    }

    private function checkBasicInfo()
    {
        $configModel = $this->getModel('config');
        $downloadId = $configModel->getSetting('sc_download_id');
        $appId = $configModel->getSetting('facebook_app_id');
        $secretKey = $configModel->getSetting('facebook_secret_key');
        if (!$downloadId || !$appId || !$secretKey)
        {
            $app = JFactory::getApplication();
            $app->enqueueMessage('Basic Info Errors Found! Please correct the errors listed before continuing.', 'error');

            $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=basicinfo');
            $this->redirect();
        }
    }

    public function getBasicInfo()
    {
        $configModel = $this->getModel('config');
        $subscriberId = $configModel->getSetting('sc_download_id');
        $appId = $configModel->getSetting('facebook_app_id');
        $secretKey = $configModel->getSetting('facebook_secret_key');

        $this->view->assignRef('subscriberId', $subscriberId);
        $this->view->assignRef('fbSecretKey', $secretKey);
        $this->view->assignRef('fbAppId', $appId);
    }

    public function saveBasicInfo()
    {
        $subscriberId = JRequest::getString('subscriberId');
        $fbAppId = JRequest::getString('facebook_app_id');
        $fbSecretKey = JRequest::getString('facebook_secret_key');

        $configModel = $this->getModel('config');
        $configModel->update('sc_download_id', $subscriberId);
        $configModel->update('facebook_app_id', $fbAppId);
        $configModel->update('facebook_secret_key', $fbSecretKey);

        $autotuneModel = $this->getModel('autotune');
        if (!$autotuneModel->validateApp($fbAppId, $fbSecretKey))
        {
            $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=basicinfo');
            $this->redirect();
        }

        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    private function setupCheckErrors()
    {
        if (defined('JFBCDEV'))
        {
            $domain = 'http://localhost/autotune/start.php';
            $baseUrl = 'http://www.sourcecoast.com';
        }
        else
        {
            $domain = 'http://www.sourcecoast.com/autotune/start.php';
            $baseUrl = JURI::root();
        }
        $baseUrl = base64_encode(urlencode($baseUrl));

        $autotuneModel = $this->getModel('autotune');
        $configModel = $this->getModel('config');
        $subscriptionId = $configModel->getSetting('sc_download_id');
        $query = '?baseUrl=' . $baseUrl . '&subscriptionId=' . $subscriptionId . '&task=jfbconnect.errorStart&format=html&'.$autotuneModel->getVersionURLQuery();

        $iframeUrl = $domain . $query;
        $this->view->assignRef('iframeUrl', $iframeUrl);
    }

    public function saveAppConfig()
    {
        $autotuneModel = $this->getModel('autotune');
        $settings = $autotuneModel->getAppValuesToSave(false);
        $this->updateFBApplication($settings);

        // Always set the migrations
        $migrations = $autotuneModel->getAppMigrationsToSave();
        $this->updateFBApplication($migrations);

        // Update the database with the new app info
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    public function saveAppRecommendations()
    {
        $autotuneModel = $this->getModel('autotune');
        $settings = $autotuneModel->getAppValuesToSave(true);
        $this->updateFBApplication($settings);

        // Always set the migrations
        $migrations = $autotuneModel->getAppMigrationsToSave();
        $this->updateFBApplication($migrations);

        // Update the database with the new app info
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    private function updateFBApplication($settings)
    {
        $query = "?";
        foreach ($settings as $setting)
            $query .= $setting->name . '=' . $setting->value . "&";

        $query = rtrim($query, '&');

        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $appId = $jfbcLibrary->get('facebookAppId', '');
        $result = $jfbcLibrary->api($appId . $query, null, false, 'POST');
    }

    public function publishPlugin()
    {
        $name = JRequest::getString('pluginName');
        $status = JRequest::getInt('pluginStatus');
        $autotuneModel = $this->getModel('autotune');
        $autotuneModel->publishPlugin($name, $status);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=siteconfig');
        $this->redirect();
    }
}