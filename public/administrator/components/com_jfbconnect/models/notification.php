<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JFBConnectModelNotification extends JModel
{
    var $_pagination = null;

    function __construct()
    {
        parent::__construct();
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int)$array[0]);
    }

    function setId($id)
    {
        $this->_id = $id;
        $this->_data = null;
    }

    function getRows($requestID, $fbUserToID, $fbUserFromID)
    {
        $app =JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

        $query = "SELECT n.*, u.j_user_id AS joomla_user_to, u2.j_user_id AS joomla_user_from FROM ((#__jfbconnect_notification n LEFT JOIN #__jfbconnect_user_map u ON n.fb_user_to=u.fb_user_id) LEFT JOIN #__jfbconnect_user_map u2 ON fb_user_from=u2.fb_user_id)";
        $query .= $this->getWhereClause($requestID, $fbUserToID, $fbUserFromID);
        $query .= $this->getFilters();
        $query .= " ORDER BY " . $filter_order . " " . $filter_order_Dir . " ";
        $this->_db->setQuery($query, $limitstart, $limit);
        $rows = $this->_db->loadObjectList();
        return $rows;
    }

    function getTotal($requestID, $fbUserToID, $fbUserFromID)
    {
        $query = "SELECT COUNT(*) FROM #__jfbconnect_notification" . $this->getWhereClause($requestID, $fbUserToID, $fbUserFromID);
        $query .= $this->getFilters();
        $this->_db->setQuery($query);
        $total = $this->_db->loadResult();
        return $total;
    }

    function getWhereClause($requestID, $fbUserToID, $fbUserFromID)
    {
        $whereQuery = '';
        $where = array();

        if ($requestID)
            $where[] = 'jfbc_request_id=' . $this->_db->quote($requestID);
        if ($fbUserToID)
            $where[] = 'fb_user_to=' . $this->_db->quote($fbUserToID);
        if ($fbUserFromID)
            $where[] = 'fb_user_from=' . $this->_db->quote($fbUserFromID);

        if (count($where))
            $whereQuery = " WHERE " . implode(" AND ", $where);

        return $whereQuery;
    }

    function getFilters()
    {
        $app =JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $query = '';
        if ($search != '') {
            $query .= " AND (fb_request_id LIKE '%" . $search . "%'" .
                    " OR fb_user_to LIKE '%" . $search . "%'" .
                    " OR fb_user_from LIKE '%" . $search . "%'" .
                    " OR jfbc_request_id LIKE '%" . $search . "%'" .
                    " OR status LIKE '%" . $search . "%'" .
                    " OR created LIKE '%" . $search . "%'" .
                    " OR modified LIKE '%" . $search . "%')";
        }
        return $query;
    }

    function &getData()
    {
        if (empty($this->_data)) {
            $query = 'SELECT * FROM #__jfbconnect_notification' .
                    ' WHERE id = ' . $this->_db->quote($this->_id);

            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data) {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->fb_request_id = 0;
            $this->_data->fb_user_to = 0;
            $this->_data->fb_user_from = 0;
            $this->_data->jfbc_request_id = 0;
            $this->_data->status = 0;
            $this->_data->created = null;
            $this->_data->modified = null;
        }
        return $this->_data;
    }

    function store()
    {
        $row = &$this->getTable("JFBConnectNotification", "Table");
        $data = JRequest::get('post');
        if (!isset($data['id']))
            $data['created'] = JFactory::getDate()->toMySQL();
        else
            $data['modified'] = JFactory::getDate()->toMySQL();

        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store()) {
            $this->setError($row->getErrorMsg());
            return false;
        }
        return true;
    }

    function expireNotifications()
    {
        $now = $this->_db->quote(JFactory::getDate()->toMySQL());
        $query = "UPDATE #__jfbconnect_notification SET status = 2, modified = " . $now .
                "WHERE status = 0 AND (created < " . $now . " - INTERVAL 14 DAY)";
        $this->_db->setQuery($query);
        $this->_db->query();
    }
}