<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewRequest extends JView
{
    function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
        $filter_published = $app->getUserStateFromRequest($option . $view . 'filter_published', 'filter_published', -1, 'int');
        $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $model = $this->getModel();
        $rows = $model->getRows();
        $this->assignRef('rows', $rows);

        $lists = array();
        $lists ['search'] = $search;

        if (!$filter_order) {
            $filter_order = 'id';
        }
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        $filter_published_options[] = JHTML::_('select.option', -1, '-- Request State --');
        $filter_published_options[] = JHTML::_('select.option', 1, 'Published');
        $filter_published_options[] = JHTML::_('select.option', 0, 'Unpublished');
        $lists['published'] = JHTML::_('select.genericlist', $filter_published_options, 'filter_published', 'onchange="javascript:this.form.submit()"', 'value', 'text', $filter_published);

        $this->assignRef('lists', $lists);

        $dateFormat = "%m/%d/%Y";
        $this->assignRef('dateFormat', $dateFormat);
        $db = JFactory::getDBO();
        $nullDate = $db->getNullDate();
        $this->assignRef('nullDate', $nullDate);

        $total = $model->getTotal();
        jimport('joomla.html.pagination');

        $pageNav = new JPagination ($total, $limitstart, $limit);
        $this->assignRef('page', $pageNav);

        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'controllers' . DS . 'canvas.php');
        $isCanvasIncorrect = !JFBConnectControllerCanvas::isCanvasSetupCorrect();
        $this->assignRef('isCanvasIncorrect', $isCanvasIncorrect);

        //$ordering = (($this->lists['order'] == 'ordering' || $this->lists['order'] == 'category') && (!$this->filter_trash));
        //$this->assignRef('ordering', $ordering);

        parent::display($tpl);
    }

    function edit()
    {
        $model = $this->getModel();
        $data = $model->getData();
        $this->assignRef('request', $data);

        $total = 0;
        $pending = 0;
        $read = 0;
        $expired = 0;

        $model->getNotificationTotals($data->id, $total, $pending, $read, $expired);
        $this->assignRef('totalNotifications', $total);
        $this->assignRef('pendingNotifications', $pending);
        $this->assignRef('readNotifications', $read);
        $this->assignRef('expiredNotifications', $expired);

        parent::display();
    }

    function previewSend()
    {
        $model = $this->getModel();
        $requestId = JRequest::getVar('id', 'POST');
        $model->setId($requestId);
        $data = $model->getData();
        $this->assignRef('request', $data);

        $app = JFactory::getApplication();
        $app->setUserState('jfbconnect.request.requestId', $requestId);

        $fbIds = JRequest::getVar('fbIds', null, '', 'array');
        if (count($fbIds) > 0)
        {
            $totalUsers = count($fbIds);
            $sendToAll = false;
            $app->setUserState('jfbconnect.request.fbIds', $fbIds);
        }
        else // Send to ALL users
        {
            $usermapModel = $this->getModel('usermap');
            $totalUsers = $usermapModel->getTotalMappings(false);
            $this->assignRef('totalMappings', $totalUsers);
            $sendToAll = true;
        }
        $app->setUserState('jfbconnect.request.sendToAll', $sendToAll);

        $this->assignRef('totalUsers', $totalUsers);
        $this->assignRef('sendToAll', $sendToAll);
        $this->assignRef('fbIds', $fbIds);
        parent::display();
    }
}
