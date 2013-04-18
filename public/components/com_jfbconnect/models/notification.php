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
    var $_fbUserId = null;
    var $_requestIds = null;

    function __construct()
    {
        parent::__construct();
    }

    function setFbUserId($fbUserId)
    {
        $this->_fbUserId = JFilterInput::clean($fbUserId, 'ALNUM');
    }

    // Clean and set the request IDs. Can be either comma separated (straight from URL) or already in a CLEANED! array
    function setFbRequestIds($fbRequestIds)
    {
        if (!is_array($fbRequestIds)) {
            $fbRequestIds = explode(',', $fbRequestIds);
            $requestIds = array();
            foreach ($fbRequestIds as $fbRequestId)
                $requestIds[] = JFilterInput::clean($fbRequestId, 'ALNUM'); // ALNum because int gets maxed out

            $fbRequestIds = array_unique($requestIds);

        }
        $this->_fbRequestIds = $fbRequestIds;
    }

    function getRequestsToDelete()
    {
        $del = array();
        // Can't delete requests if user hasn't approved the app, per bugs:
        // https://developers.facebook.com/bugs/239476836116522
        // https://developers.facebook.com/bugs/202883726463009
        if (!$this->_fbUserId)
            return $del;

        foreach ($this->_fbRequestIds as $req)
            $del[] = $req . "_" . $this->_fbUserId;

        return $del;
    }

    function markAsRead()
    {
        // Can't mark request as read if User ID isn't detected (see bugs above)
        if (!$this->_fbUserId)
            return;

        $now = JFactory::getDate()->toMySQL();
        $query = "UPDATE #__jfbconnect_notification SET modified = '" . $now . "', status = 1 WHERE fb_user_to = " . $this->_db->quote($this->_fbUserId) .
                " AND (fb_request_id = '";
        $query .= implode("' OR fb_request_id = '", $this->_fbRequestIds);
        $query .= "')";
        $this->_db->setQuery($query);
        $this->_db->query();
    }

    function getRedirect()
    {
        $query = "SELECT r.destination_url rDestinationUrl, breakout_canvas FROM #__jfbconnect_request r INNER JOIN #__jfbconnect_notification n ON r.id = n.jfbc_request_id " .
                " WHERE n.fb_request_id IN (" . implode(', ', $this->_fbRequestIds) . ") ORDER BY n.created DESC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        $redirectInfo = new stdClass();
        $redirectInfo->breakout_canvas = $data->breakout_canvas;
        $redirectInfo->destination_url = $data->rDestinationUrl;

        return $redirectInfo;
    }
}