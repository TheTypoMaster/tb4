<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JFBConnectControllerUserMap extends JFBConnectController
{
    function __construct()
    {
        JRequest::setVar('view', 'UserMap');
        parent::__construct();
    }

    function display($cachable = false, $urlparams = false)
    {
        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($usermapModel, false);

        $viewLayout = JRequest::getCmd('layout', 'list');
        $this->view->setLayout($viewLayout);

        // Add Toolbar icons
        JToolBarHelper::custom('selectRequest', 'send', 'send', 'Send Request', true);
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('jfbcprofiles');
        $profilePlugins = $app->triggerEvent('jfbcProfilesGetPlugins');
        $pluginNames = array();
        foreach ($profilePlugins as $plugin)
        {
            if ($plugin->canImportConnections())
                $pluginNames[] = $plugin->getName();
        }
        if (count($pluginNames) != 0) {
            $doc = JFactory::getDocument();
            $doc->addCustomTag('<script>var jfbcImportMsg = "This will import previous Facebook / Joomla connections from the following profile plugins:\n' . implode('\n', $pluginNames) . '\n\nThis will update any existing JFBConnect Joomla User and Facebook connections. Select OK to import these connections.";</script>');
            $html = "<a href=\"#\" onclick=\"javascript:if(confirm(jfbcImportMsg)){submitbutton('importConnections');}\" class=\"toolbar\">\n";
            $html .= "<span class=\"icon-32-upload\" title=\"Import Connections\">\n";
            $html .= "</span>\n";
            $html .= "Import Connections\n";
            $html .= "</a>\n";
            $bar = JToolBar::getInstance('toolbar');
            //$bar->appendButton('Confirm', "Import connections from the enabled profile plugins? Ensure you've enabled only the plugins for 3rd party components that you'd want to import previous connections from.", 'upload', 'Import Connections', 'importConnections', false, false);
            $bar->appendButton('Custom', $html);
        }
        JToolBarHelper::deleteList('Are you sure you want to delete the selected records?');

        $this->view->display();
    }

    function remove()
    {
        $model = $this->getModel('UserMap');

        if (!$model->delete()) {
            $msg = 'Error: One or More Rows Could not be Deleted';
        }
        else
        {
            $msg = 'Row(s) Deleted';
        }

        $this->display();
    }

    function importConnections()
    {
        $app =JFactory::getApplication();
        JPluginHelper::importPlugin('jfbcprofiles');
        $profilePlugins = $app->triggerEvent('jfbcImportConnections');
        $msg = "Connections successfully imported.";
        $app->enqueueMessage($msg);
        $this->display();
    }

    function selectRequest()
    {
        JToolBarHelper::custom("previewSend", "forward", "forward", "Preview Send", false);

        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($usermapModel, false);

        $requestModel = $this->getModel('request');
        $this->view->setModel($requestModel, false);

        $this->view->setLayout('select_request');
        $this->view->selectRequest();
    }
}