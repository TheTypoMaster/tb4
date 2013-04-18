<?php

defined('_JEXEC') or die();

jimport('mobileactive.database.query');

/**
 * Factory class for DatabaseQuery and elements.
 *
 * @author declan.kennedy
 * @package mobileactive
 * @deprecated Not maintained at all, DatabaseQueryTable now has a built in entity factory.
 */
class DBFactory
{
	/**
	 * Return a new query object using the supplied table name
	 *
	 * @param mixed $table
	 * @return DatabaseQuery
	 */
	public static function getQuery($table)
	{
		if(!is_object($table)) {
			$table = new DatabaseQueryTable($table);
		}

		return new DatabaseQuery($table);
	}

	/**
	 * Return a new table object, optionally setting a supplied column list
	 *
	 * @param string 	$name
	 * @param array 	$column_list
	 * @param DatabaseQueryTable
	 */
	public static function getTable($name, $column_list = array())
	{
		return self::getTableForSave($name, $column_list);
	}

	/**
	 * Get a table with added columns. Alias list indicates whether to treat the column list as one
	 * containing aliases or values.
	 *
	 * @param string 	$name
	 * @param array 	$column_list
	 * @param bool 		$alias_list
	 * @return DatabaseQueryTable
	 */
	private static function _getTable($name, $column_list = array(), $alias_list = false)
	{
		$table = new DatabaseQueryTable($name);

		foreach($column_list as $member => $value) {
			if($alias_list) {
				$column_alias = $value;
				$column_value = null;
			} else {
				$column_alias = null;
				$column_value = $value;
			}

			$table->addColumn(new DatabaseQueryTableColumn($member, $column_alias, $column_value));
		}

		return $table;
	}

	/**
	 * Get a table with added columns, assuming a value list not an alias list.
	 *
	 * @param string 	$name
	 * @param array 	$column_list
	 * @return DatabaseQueryTable
	 */
	public static function getTableForSave($name, $column_list = array())
	{
		return self::_getTable($name, $column_list);
	}

	/**
	 * Get a table with added columns, assuming an alias list not a value list.
	 *
	 * @param string 	$name
	 * @param array 	$column_list
	 * @return DatabaseQueryTable
	 */
	public static function getTableForLoad($name, $column_list = array())
	{
		return self::_getTable($name, $column_list, true);
	}

	/**
	 * Get a column object
	 *
	 * @param string 	$name
	 * @param string 	$alias
	 * @param mixed 	$value
	 * @return DatabaseQueryTableColumn
	 */
	public static function getColumn($name, $alias = null, $value = null)
	{
		return new DatabaseQueryTableColumn($name, $alias, $value);
	}

	public static function getSubquery($name, $alias)
	{
		return new DatabaseQueryTableSubquery($name, $alias);
	}

	public static function getFunction($name, $alias, $type)
	{
		return new DatabaseQueryTableFunction($name, $alias, $type);
	}

	/**
	 * Get a where object
	 *
	 * @param string 	$column_name
	 * @param mixed 	$value
	 * @param integer 	$context
	 * @param integer 	$operator
	 * @return DatabaseQueryTableWhere
	 */
	public static function getWhere($column_name, $value, $context = null, $operator = null)
	{
		$column = new DatabaseQueryTableColumn($column_name, null, $value);
		return new DatabaseQueryTableWhere($column, $context, $operator);
	}

	/**
	 * Get a join object
	 *
	 * @param mixed 	$table
	 * @param string 	$column_name_1
	 * @param string 	$column_name_2
	 * @param integer 	$type
	 * @return DatabaseQueryTableJoin
	 */
	public static function getJoin($table, $column_name_1, $column_name_2, $type = null)
	{
		if(!is_object($table)) {
			$table = new DatabaseQueryTable($table);
		}

		$column_1 = new DatabaseQueryTableColumn($column_name_1);
		$column_2 = new DatabaseQueryTableColumn($column_name_2);

		return new DatabaseQueryTableJoin($table, $column_1, $column_2, $type);
	}

	/**
	 * Get a group object
	 *
	 * @param string $column_name
	 * @return DatabaseQueryTableGroup
	 */
	public static function getGroup($column_name)
	{
		$column = new DatabaseQueryTableColumn($column_name);
		return new DatabaseQueryTableGroup($column);
	}

	/**
	 * Get an order object
	 *
	 * @param string 	$column_name
	 * @param integer 	$direction
	 * @return DatabaseQueryTableOrder
	 */
	public static function getOrder($column_name, $direction = null)
	{
		$column = new DatabaseQueryTableColumn($column_name);
		return new DatabaseQueryTableOrder($column, $direction);
	}
}