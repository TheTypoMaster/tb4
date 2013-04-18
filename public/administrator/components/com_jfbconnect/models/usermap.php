<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JFBConnectModelUserMap extends JModel
{

    var $_pagination = null;

    function __construct()
    {
        parent::__construct();
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int) $array[0]);
    }

    function setId($id)
    {
        $this->_id = $id;
        $this->_data = null;
    }

    function &getData($j_user_id = null)
    {
        if (empty($this->_data))
        {
            if (!$j_user_id)
            {
                $query = "SELECT um.*, " .
                         "(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE fb_user_from = um.fb_user_id) sent, ".
                         "(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE fb_user_to = um.fb_user_id) received ".
                         "FROM #__jfbconnect_user_map um ".
                        ' WHERE id = ' . $this->_db->quote($this->_id);
            }
            else
            {
                $query = "SELECT um.*, " .
                         "(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE fb_user_from = um.fb_user_id) sent, ".
                         "(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE fb_user_to = um.fb_user_id) received ".
                         "FROM #__jfbconnect_user_map um ".
                        ' WHERE j_user_id = ' . $this->_db->quote($j_user_id);
            }

            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data)
        {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->created_at = JFactory::getDate()->toMySQL();
            $this->_data->updated_at = JFactory::getDate()->toMySQL();
            $this->_data->j_user_id = $j_user_id;
            $this->_data->fb_user_id = null;
            $this->_data->sent = null;
            $this->_data->authorized = 1;
            $this->_data->received = null;
        }
        return $this->_data;
    }

    function &getPagination()
    {
        if ($this->_pagination == null)
        {
            $this->getList();
        }
        return $this->_pagination;
    }

    function &getViewLists()
    {
        $app = JFactory::getApplication();

        //Search
        $search = $app->getUserStateFromRequest('com_jfbconnect.usermap.search', 'search', '', 'string');
        $search = JString::strtolower($search);
        $lists['search'] = $search;

        //Order
        $filter_order = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

        if (!$filter_order) {
            $filter_order = 'id';
        }
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        return $lists;
    }

    function getList()
    {
        // Lets load the data if it doesn't already exist
        if (empty($this->_listData))
        {
            $app = JFactory::getApplication();

            $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
            $limitstart = $app->getUserStateFromRequest('com_jfbconnect.usermap.limitstart', 'limitstart', 0, 'int');
            $filter_order = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order', 'filter_order', 'id', 'cmd');
            $filter_order_Dir = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

            if (!$filter_order) {
                $filter_order = 'id';
            }

            //Search values
            $search = $app->getUserStateFromRequest("com_jfbconnect.usermap.search", 'search', '', 'string');
            $search = JString::strtolower($search);

            // Get our row count for pagination
            $query = "SELECT COUNT(*) count FROM #__jfbconnect_user_map um";
            if ($search != '') //Set up where clause using search
            {
                $where = " WHERE um.j_user_id LIKE '%".$search."%'" .
                            " OR um.fb_user_id LIKE '%".$search."%'" .
                            " OR ju.name LIKE '%".$search."%'" .
                            " OR ju.email LIKE '%".$search."%'" .
                            " OR ju.username LIKE '%".$search."%'";

                $query.= " INNER JOIN #__users ju ON ju.id=um.j_user_id" . $where;
            }
            $this->_db->setQuery($query);
            $total = $this->_db->loadResult();

            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($total, $limitstart, $limit);

            // Get our rows
            $query = "SELECT um.*, "
                    ."(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE fb_user_from = um.fb_user_id) sent, "
                    ."(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE fb_user_to = um.fb_user_id) received "
                    ."FROM #__jfbconnect_user_map um";
            if ($search != '')
            {
                $query .= " INNER JOIN #__users ju ON ju.id = um.j_user_id" . $where;
            }
            $query .= " ORDER BY " . $filter_order . " " . $filter_order_Dir . " ";
            if ($limit > 0)
            {
                $query .= ' LIMIT ' . $limitstart . ', ' . $limit;
            }
            $this->_listData = $this->_getList($query);
        }

        return $this->_listData;
    }

    function store()
    {
        $row = &$this->getTable();
        $data = JRequest::get('post');
        if (!$row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        $row->updated_at = JFactory::getDate()->toMySQL();
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($row->getErrorMsg());
            return false;
        }
        return true;
    }

    function update($fb_user_id)
    {
        if ($this->_data->fb_user_id != $fb_user_id)
        {
            $row = &$this->getTable();
            $row->id = $this->_data->id;
            $row->created_at = $this->_data->created_at;
            $row->updated_at = JFactory::getDate()->toMySQL();
            $row->j_user_id = $this->_data->j_user_id;
            $row->fb_user_id = $fb_user_id;
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
        }
        return true;
    }

    function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $row = & $this->getTable();
        if (count($cids))
        {
            foreach ($cids as $cid)
            {
                if (!$row->delete($cid))
                {
                    $this->setError($row->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    function deleteMapping($fbUid)
    {
        $query = "DELETE FROM #__jfbconnect_user_map " .
                "WHERE fb_user_id=" . $this->_db->quote($fbUid);
        $this->_db->setQuery($query);
        $this->_db->query();
    }

    function deleteMappingWithJoomlaId($userId)
    {
        $query = "DELETE FROM #__jfbconnect_user_map " .
                "WHERE j_user_id=" . $this->_db->quote($userId);
        $this->_db->setQuery($query);
        $this->_db->query();
    }

    function getJoomlaUserId($fbUid)
    {
        $query = "SELECT j_user_id FROM #__jfbconnect_user_map " .
                "WHERE fb_user_id=" . $this->_db->quote($fbUid);
        $this->_db->setQuery($query);
        $joomlaId = $this->_db->loadResult();
        return $joomlaId;
    }

    function getFacebookUserId($joomlaId)
    {
        $query = "SELECT fb_user_id FROM #__jfbconnect_user_map " .
                "WHERE j_user_id=" . $this->_db->quote($joomlaId);
        $this->_db->setQuery($query);
        $facebookId = $this->_db->loadResult();
        return $facebookId;
    }

    function mapUser($fbUid, $jUserId = null)
    {
        if ($jUserId)
            $user = JUser::getInstance($jUserId);
        else
            $user = JFactory::getUser();

        #echo "Mapping user<br>";
        if ($user->id && $fbUid)
        {
            #echo "User and FB id found, ".$user->id.", $fbUid<br>";
            JModel::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models');
            $model = JModel::getInstance('JFBConnectModelUserMap');
            $jMappingId = self::getJoomlaUserId($fbUid);
            if ($user->get('id') != $jMappingId)
            {
                #echo "Joomla ID not mapped to this user, updating<br>";
                if ($jMappingId) # delete old mapping if it exists
                    $model->deleteMapping($fbUid);

                $model->getData($user->id);
                #echo "mapUser: ".$user->id." => ".$fbUid.": ";
                if ($model->update($fbUid))
                {
                    #echo "Map updated<br>";
                    return true;
                }
                else
                {
                    #echo "Map error<br>";
                    return false;
                }
            }
            else
                return true; // No mapping necessary

        }
        else
        {
            return false;
        }
    }

    function getTotalMappings($includeBlocks = true)
    {
        $query = "SELECT count(*) FROM #__jfbconnect_user_map jfbc ";
        if (!$includeBlocks)
        {
            $query .= " INNER JOIN #__users ju ON ju.id=jfbc.j_user_id WHERE ju.block = 0 AND jfbc.authorized = 1";
        }
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    // Simple array of all active FB Ids. Used for sending requests to all users
    function getActiveUserFbIds($start = null, $length = null)
    {
        $query = "SELECT fb_user_id FROM #__jfbconnect_user_map jfbc ".
                " INNER JOIN #__users ju ON ju.id=jfbc.j_user_id ".
                " WHERE ju.block = 0 AND jfbc.authorized = 1";
        $this->_db->setQuery($query, $start, $length);
        return $this->_db->loadResultArray();
    }

    function getJoomlaUserIdFromEmail($email)
    {
        $query = "SELECT id FROM #__users WHERE email = " . $this->_db->quote($email);
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }
    function getFbIdsFromList($array)
    {
        $query = "SELECT fb_user_id FROM #__jfbconnect_user_map WHERE id IN (".implode(', ', $array). ")";
        $this->_db->setQuery($query);
        return $this->_db->loadResultArray();
    }

    function updateUserToken($jUserId, $token)
    {
        $query = "UPDATE #__jfbconnect_user_map SET " .
                "authorized=1, " .
                "access_token = " . $this->_db->quote($token) . ", " .
                "updated_at = " . $this->_db->quote(JFactory::getDate()->toMySQL()) .
                " WHERE j_user_id = " . $this->_db->quote($jUserId);
        $this->_db->setQuery($query);
        $this->_db->query();
    }

    // Used for the callback from Facebook if the user has de-authorized the application
    function setAuthorized($fbUserId, $authorize)
    {
        $query = "UPDATE #__jfbconnect_user_map SET ".
                "authorized = " . $this->_db->quote($authorize) . ", " .
                "updated_at = " . $this->_db->quote(JFactory::getDate()->toMySQL()) .
                " WHERE fb_user_id = " . $this->_db->quote($fbUserId);
        $this->_db->setQuery($query);
        $this->_db->query();
    }

}