<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

class TableJFBConnectRequest extends JTable
{
	var $id = null;
	var $published = null;
    var $title = null;
    var $message = null;
    var $destination_url = null;
    var $thanks_url = null;
	var $breakout_canvas = false;
    var $created = null;
    var $modified = null;

	function TableJFBConnectRequest(&$db)
	{
		parent::__construct('#__jfbconnect_request', 'id', $db);
	}
}