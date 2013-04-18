<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewCanvas extends JView
{
    function display($tpl = null)
    {
        require_once(JPATH_COMPONENT_SITE . DS . 'libraries' . DS . 'facebook.php');
        $model = $this->getModel('config');
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
            
        
            require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_templates' . DS . 'helpers' . DS . 'template.php');
            $templateHelper = new TemplatesHelper();
            $templates = $templateHelper->parseXMLTemplateFiles(JPATH_SITE . DS . 'templates');
         // SC15
         // SC16

        // Add the "Don't Override" option to set no special template
        $defaultTemplate = new stdClass();
        $defaultTemplate->directory = -1;
        $defaultTemplate->name = "- No Override. Use Default Template - ";
        array_unshift($templates, $defaultTemplate);

        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'controllers' . DS . 'canvas.php');
        $canvasProperties = JFBConnectControllerCanvas::setupCanvasProperties();

        $canvasTabTemplate = $model->getSetting('canvas_tab_template', -1);
        $canvasCanvasTemplate = $model->getSetting('canvas_canvas_template', -1);

        $this->assignRef('canvasProperties', $canvasProperties);
        $this->assignRef('canvasTabTemplate', $canvasTabTemplate);
        $this->assignRef('canvasCanvasTemplate', $canvasCanvasTemplate);
        $this->assignRef('templates', $templates);
        $this->assignRef('model', $model);
        $this->assignRef('jfbcLibrary', $jfbcLibrary);
        parent::display($tpl);
    }

}
