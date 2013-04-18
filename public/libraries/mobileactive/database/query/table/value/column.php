<?php

defined('_JEXEC') or die();

jimport('mobileactive.database.query.table.value');

/**
 * Used to handle edge cases where a column is used as a value such as back referencing in subqueries
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableValueColumn extends DatabaseQueryTableValue
{
	protected $_escape = false;

	/**
	 * Wraps the column object to generate SQL
	 *
	 * @param integer $type
	 * @return string
	 */
	public function getSQL($type = null)
	{
		return $this->getValue()->getSQL($type);
	}
}