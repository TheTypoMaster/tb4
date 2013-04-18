<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

class TableConfig extends JTable
{
	var $id = null;

	var $setting = null;
	var $value = null;

	var $created_at = null;
	var $updated_at = null;

	function TableConfig(&$db)
	{
		parent::__construct('#__jfbconnect_config', 'id', $db);
	}
}