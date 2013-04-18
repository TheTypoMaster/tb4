<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewNotification extends JView
{
    function display($tpl = null)
    {
        $app =JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
        $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $requestID = JRequest::getInt('requestid', 0);
        $fbUserToID = JRequest::getVar('fbuserto', 0);
        $fbUserFromID = JRequest::getVar('fbuserfrom', 0);

        $model = $this->getModel();
        $rows = $model->getRows($requestID, $fbUserToID, $fbUserFromID);
        $this->assignRef('rows', $rows);

        $lists = array();
        $lists ['search'] = $search;

        if (!$filter_order) {
            $filter_order = 'id';
        }
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        $this->assignRef('lists', $lists);

        $dateFormat = "%m/%d/%Y";
        $this->assignRef('dateFormat', $dateFormat);
        $db = JFactory::getDBO();
        $nullDate = $db->getNullDate();
        $this->assignRef('nullDate', $nullDate);

        $total = $model->getTotal($requestID, $fbUserToID, $fbUserFromID);
        jimport('joomla.html.pagination');

        $pageNav = new JPagination ($total, $limitstart, $limit);
        $this->assignRef('page', $pageNav);

        //$ordering = (($this->lists['order'] == 'ordering' || $this->lists['order'] == 'category') && (!$this->filter_trash));
        //$this->assignRef('ordering', $ordering);

        parent::display($tpl);
    }
}
