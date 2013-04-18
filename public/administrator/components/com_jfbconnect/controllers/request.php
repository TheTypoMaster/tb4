<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JFBConnectControllerRequest extends JFBConnectController
{

    function __construct()
    {
        JRequest::setVar('view', 'Request');
        parent::__construct();
    }

    function display($cachable = false, $urlparams = false)
    {
        $viewLayout = JRequest::getCmd('layout', 'default');
        $this->view->setLayout($viewLayout);

        if ($viewLayout == "default") {
            JToolBarHelper::addNew('add', 'New');
            JToolBarHelper::deleteList('Are you sure you want to delete this request? Any associated notifications will also be removed. Note, if there are any pending notifications, this request will not be deleted.');
        }
        $task = JRequest::getCmd('task', "display");
        if ($task == "")
            $task = 'display'; // Needed for ordering tasks

        $requestModel = $this->getModel('request');
        $this->view->setModel($requestModel, true);
        $this->view->$task();
    }

    function add()
    {
        JRequest::setVar('task', 'edit');
        $this->edit();
    }

    function remove()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel('request');

        if ($model->delete())
            $app->enqueueMessage("Request Deleted!");

        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    function edit()
    {
        JRequest::setVar('layout', 'edit');
        JToolBarHelper::custom('previewSend', 'send', 'send', 'Send to All Users', false);
        JToolBarHelper::save('apply', 'Save');
        JToolBarHelper::cancel('cancel', 'Cancel');
        $this->display();
    }

    function cancel()
    {
        JRequest::setVar('layout', 'default');
        JRequest::setVar('task', 'display');
        $this->display();
    }

    function apply()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel('request');
        $model->store();
        $app->enqueueMessage("Request Saved!");
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    function publish()
    {
        $model = $this->getModel('request');
        $model->setPublished(true);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    function unpublish()
    {
        $model = $this->getModel('request');
        $model->setPublished(false);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    function enable_breakout_canvas()
    {
        $model = $this->getModel('request');
        $model->setBreakoutCanvas(true);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    function disable_breakout_canvas()
    {
        $model = $this->getModel('request');
        $model->setBreakoutCanvas(false);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    function previewSend()
    {
        $app =JFactory::getApplication();
        $inProgress = $app->getUserState('jfbconnect.request.inProgress', false);
        if ($inProgress)
            $app->redirect('index.php?option=com_jfbconnect&controller=request', "Sending is in progress! Do not navigate while sending!", 'error');

        JRequest::setVar('hidemainmenu', 1); // Hide the menus
        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($usermapModel, false);

        JRequest::setVar('layout', 'send');
        JToolBarHelper::cancel('cancel', 'Cancel');

        // Reset stuff
        $app = JFactory::getApplication();
        $app->setUserState('jfbconnect.request.requestId', null);
        $app->setUserState('jfbconnect.request.fbIds', null);
        $app->setUserState('jfbconnect.request.sendToAll', null);
        $app->setUserState('jfbconnect.request.sendSuccess', 0);
        $app->setUserState('jfbconnect.request.sendFail', 0);
        $app->setUserState('jfbconnect.request.sendCount', 0);

        $this->display();
    }

    function send()
    {
        $app =JFactory::getApplication();
        $app->setUserState('jfbconnect.request.inProgress', true);

        require_once(JPATH_SITE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php');
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();

        $sendLimit = 15;
        $jfbcRequestId = $app->getUserState('jfbconnect.request.requestId');
        $sendToAll = $app->getUserState('jfbconnect.request.sendToAll');
        $sendCount = $app->getUserState('jfbconnect.request.sendCount', 0);
        $sendSuccess = $app->getUserState('jfbconnect.request.sendSuccess', 0);
        $sendFail = $app->getUserState('jfbconnect.request.sendFail', 0);

        $model = $this->getModel('request');
        $model->setId($jfbcRequestId);
        $request = $model->getData();

        $usermapModel = $this->getModel('usermap');

        if (!$sendToAll)
        {
            $fbIds = $app->getUserState('jfbconnect.request.fbIds');
            $toUsers = array_slice($fbIds, 0, $sendLimit);
            $app->setUserState('jfbconnect.request.fbIds', array_slice($fbIds, $sendLimit));
        }
        else
            $toUsers = $usermapModel->getActiveUserFbIds($sendCount, $sendLimit);

        $message = $request->message;
        $utf8message = utf8_encode($message);
        $params['message'] = $utf8message;

        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'tables');
        $app = JFactory::getApplication();
        $data = array();
        $data['fb_user_from'] = -1;
        $data['modified'] = null;
        $data['jfbc_request_id'] = $jfbcRequestId;
        foreach ($toUsers as $toUser)
        {
            $result = $jfbcLibrary->api('/' . $toUser . '/apprequests', $params, false, null, true);
            //$result = array('request'=> '12345', 'to' => array(0=>$toUser));
            if (isset($result['request']) && $result['to'][0] == $toUser) {
                // Not using the model, as we're doing a simple store.
                $data['fb_request_id'] = $result['request'];
                $data['created'] = JFactory::getDate()->toMySQL();
                $data['fb_user_to'] = $toUser;

                $row = & JTable::getInstance('JFBConnectNotification', 'Table');
                $row->save($data);
                $sendSuccess++;
            }
            else
            {
                $usermapModel->setAuthorized($toUser, false);
                $sendFail++;
            }

            $sendCount++;
        }

        if (count($toUsers) < $sendLimit)
            $inProgress = false;
        else
            $inProgress = true;

        $app->setUserState('jfbconnect.request.sendSuccess', $sendSuccess);
        $app->setUserState('jfbconnect.request.sendFail', $sendFail);
        $app->setUserState('jfbconnect.request.sendCount', $sendCount);
        $app->setUserState('jfbconnect.request.inProgress', $inProgress);

        $return = array('sendCount' => $sendCount, 'sendSuccess' => $sendSuccess, 'sendFail' => $sendFail, 'inProgress' => $inProgress, 'sentIds' => $toUsers);
        echo json_encode($return);
        exit;
//        $this->setRedirect('index.php?option=com_jfbconnect&controller=request&view=edit&id=' . $jfbcRequestId);
    }
}