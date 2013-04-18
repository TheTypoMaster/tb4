<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JFBConnectControllerNotification extends JFBConnectController
{

    function __construct()
    {
        JRequest::setVar('view', 'Notification');
        parent::__construct();
    }

    function display($cachable = false, $urlparams = false)
    {
        $notificationModel = $this->getModel('notification');
        $this->view->setModel($notificationModel, true);

        $viewLayout = JRequest::getCmd('layout', 'default');
        $this->view->setLayout($viewLayout);

        if ($viewLayout == "default")
        {
            JToolBarHelper::back('back');
        }

        $task = JRequest::getCmd('task', "display");
        if ($task == "")
            $task = 'display'; // Needed for ordering tasks
        $this->view->$task();
    }
}