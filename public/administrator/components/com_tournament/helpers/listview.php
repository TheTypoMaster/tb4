<?php
defined('_JEXEC') or die();

class ListViewHelper
{
	public static function getParameterList($prefix = '')
	{
		global $mainframe;

		$order 	= $mainframe->getUserStateFromRequest(
			$prefix . 'filter_order',
			'filter_order',
			'id'
		);

		$direction = $mainframe->getUserStateFromRequest(
			$prefix . 'filter_order_Dir',
			'filter_order_Dir',
			'desc'
		);

		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			20
		);

		if($limit <= 0) {
			$limit = 20;
		}

		$offset = $mainframe->getUserStateFromRequest(
			$prefix . 'limitstart',
			'limitstart',
			0
		);

		return array($order, $direction, $limit, $offset);
	}
}