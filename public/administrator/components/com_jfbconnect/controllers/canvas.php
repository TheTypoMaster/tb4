<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JFBConnectControllerCanvas extends JFBConnectController
{

    function __construct()
    {
        JRequest::setVar('view', 'Canvas');
        parent::__construct();
    }

    function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('layout', 'default');
        parent::display();
    }

    function apply()
    {
        $app = JFactory::getApplication();
        $configs = JRequest::get('POST', 4);
        $model = $this->getModel('config');
        $model->saveSettings($configs);
        $app->enqueueMessage("Settings updated!");
        $this->display();
    }

    public static function setupCanvasProperties()
    {
        require_once(JPATH_COMPONENT_SITE . DS . 'libraries' . DS . 'facebook.php');
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();

        $canvasProperties = new JObject();
        $appId = $jfbcLibrary->get('facebookAppId', '');
        if ($appId)
        {
            $params = "?fields=canvas_url,secure_canvas_url,page_tab_default_name,page_tab_url,secure_page_tab_url,namespace,website_url,canvas_fluid_height,canvas_fluid_width";
            $appProps = $jfbcLibrary->api($appId.$params, null, FALSE);

            $canvasProperties->setProperties($appProps);
        }
        return $canvasProperties;
    }

    public static function isCanvasSetupCorrect()
    {
        $canvasProperties = JFBConnectControllerCanvas::setupCanvasProperties();

        $canvasName = $canvasProperties->get('namespace', "");
        $canvasUrl = $canvasProperties->get('canvas_url', '');
        $secureCanvasUrl = $canvasProperties->get('secure_canvas_url', '');

        if (!$canvasName || $canvasUrl == "" || $secureCanvasUrl == "")
        {
            return false;
        }
        else
        {
            return true;
        }
    }

}