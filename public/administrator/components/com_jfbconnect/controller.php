<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

class JFBConnectController extends JController
{
    var $view = null;
    function __construct()
    {
        parent::__construct();
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = JRequest::getCmd('view', $this->getName());
        $this->view = $this->getView($viewName, $viewType);
         //SC16
        $this->expireNotifications();
    }

    function display($cachable = false, $urlparams = false)
    {
        $configModel = $this->getModel('config');
        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($configModel, true);
        $this->view->setModel($usermapModel, false);

        $viewLayout = JRequest::getCmd('layout', 'default');
        $this->view->setLayout($viewLayout);

        $this->view->display();
    }

    public function autotune()
    {
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune');
        $this->redirect();
    }

    protected function _addSubMenus($vName = 'overview')
    {
        
    }

    function expireNotifications()
    {
        $app = JFactory::getApplication();
        // Only run the expiration query once per session
        if (!$app->getUserState('com_jfbconnect.notifications.expired', false))
        {
            $notificationModel = $this->getModel('notification');
            $notificationModel->expireNotifications();
            $app->setUserState('com_jfbconnect.notifications.expired', true);
        }
    }

}
