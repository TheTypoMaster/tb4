<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JFBConnectModelRequest extends JModel
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

    function getRows()
    {
        $app = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

        $query = "SELECT r.*, COUNT(n.jfbc_request_id) send_count";
        $query .= " FROM #__jfbconnect_request r LEFT JOIN #__jfbconnect_notification n ON r.id=n.jfbc_request_id";
        $query .= $this->getFilters();
        $query .= " GROUP BY r.id";
        $query .= " ORDER BY " . $filter_order . " " . $filter_order_Dir . " ";

        $this->_db->setQuery($query, $limitstart, $limit);
        $rows = $this->_db->loadObjectList();
        return $rows;
    }

    function getTotal()
    {
        $query = "SELECT COUNT(*) FROM #__jfbconnect_request";
        $query .= $this->getFilters();
        $this->_db->setQuery($query);
        $total = $this->_db->loadResult();
        return $total;
    }

    function getFilters()
    {
        $app = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $filter_published = $app->getUserStateFromRequest($option . $view . 'filter_published', 'filter_published', -1, 'int');
        $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $query = '';
        if ($filter_published != -1)
            $query .= " WHERE published = " . $filter_published;
        if ($search != '')
        {
            if ($query == '')
                $query .= " WHERE (";
            else
                $query .= " AND (";

            $query .= " title LIKE '%" . $search . "%'" .
                      " OR message LIKE '%" . $search . "%'" .
                      " OR destination_url LIKE '%" . $search . "%'" .
                      " OR thanks_url LIKE '%" . $search . "%')";
        }
        return $query;
    }

    function &getData()
    {
        if (empty($this->_data))
        {
            $query = 'SELECT * FROM #__jfbconnect_request' .
                     ' WHERE id = ' . $this->_db->quote($this->_id);

            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data)
        {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->published = 0;
            $this->_data->title = "";
            $this->_data->message = "";
            $this->_data->destination_url = JURI::root();
            $this->_data->thanks_url = JURI::root();
            $this->_data->breakout_canvas = false;
            $this->_data->created = null;
            $this->_data->modified = null;
        }
        return $this->_data;
    }

    function store()
    {
        $row = &$this->getTable("JFBConnectRequest", "Table");
        $data = JRequest::get('post');
        if ($data['id'] == 0)
            $data['created'] = JFactory::getDate()->toMySQL();
        else
            $data['modified'] = JFactory::getDate()->toMySQL();

        $data['breakout_canvas'] = isset($data['breakout_canvas']) ? '1' : '0';

        if (!$row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    function setPublished($published)
    {
        $row = &$this->getTable("JFBConnectRequest", "Table");
        $row->load($this->_id);
        $row->published = $published;
        $this->save($row);
    }

    function setBreakoutCanvas($breakout)
    {
        $row = &$this->getTable("JFBConnectRequest", "Table");
        $row->load($this->_id);
        $row->breakout_canvas = $breakout;
        $this->save($row);
    }

    function save($row)
    {
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    function delete()
    {
        $app = JFactory::getApplication();
        $row = &$this->getTable("JFBConnectRequest", "Table");
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');

        if (count($cids))
        {
            foreach ($cids as $cid)
            {
                $pendingNotifications = $this->getPendingNotifications($cid);
                if ($pendingNotifications > 0)
                {
                    $message = "There are " . $pendingNotifications . " pending notifications. Request not deleted.";
                    $app->enqueueMessage($message);
                    return false;
                }

                if (!$row->delete($cid))
                {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }

                if (!$this->deleteRequestNotifications($cid))
                {
                    $message = "Request deleted, but failed to delete associated notifications. Please remove manually.";
                    $app->enqueueMessage($message);
                    return false;
                }
            }
        }
        return true;
    }

    function getPendingNotifications($requestID)
    {
        $query = "SELECT COUNT(*) from #__jfbconnect_notification WHERE status=0 AND jfbc_request_id=" . $this->_db->quote($requestID);
        $this->_db->setQuery($query);
        $pendingNotifications = $this->_db->loadResult();
        return $pendingNotifications;
    }

    function deleteRequestNotifications($requestID)
    {
        $query = "DELETE FROM #__jfbconnect_notification WHERE jfbc_request_id=" . $this->_db->quote($requestID);
        $this->_db->setQuery($query);
        $result = $this->_db->query();
        return $result;
    }

    function getNotificationTotals($requestID, &$total, &$pending, &$read, &$expired)
    {
        $query = "SELECT status, COUNT(*) count FROM #__jfbconnect_notification n WHERE jfbc_request_id=" . $this->_db->quote($requestID) . " GROUP BY status;";
        $this->_db->setQuery($query);
        $allNotifications = $this->_db->loadObjectList();

        $pending = 0;
        $read = 0;
        $expired = 0;

        if($allNotifications)
        {
            foreach ($allNotifications as $notification)
            {
                if ($notification->status == 0)
                    $pending = intval($notification->count);
                else if ($notification->status == 1)
                    $read = intval($notification->count);
                else if ($notification->status == 2)
                    $expired = intval($notification->count);
            }
        }

        $total = $pending + $read + $expired;
    }

    function getPublishedRequests()
    {
        $query = "SELECT * FROM #__jfbconnect_request WHERE published = 1";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
}