<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

class TableJFBConnectNotification extends JTable
{
	var $id = null;
	var $fb_request_id = null;
    var $fb_user_to = null;
    var $fb_user_from = null;
    var $jfbc_request_id = null;
    var $status = 0;
    var $created = null;
    var $modified = null;

	function TableJFBConnectNotification(&$db)
	{
		parent::__construct('#__jfbconnect_notification', 'id', $db);
	}

}