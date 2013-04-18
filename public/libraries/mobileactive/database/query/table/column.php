<?php

defined('_JEXEC') or die();

jimport('mobileactive.object.value');

/**
 * Database query column class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableColumn extends ValueObject
{
	/**
	 * A reference to the parent table for the column
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_table = null;

	/**
	 * Column name
	 *
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Column alias, which will be generated if not provided
	 *
	 * @var string
	 */
	protected $_alias = null;

	/**
	 * Column value
	 *
	 * @var DatabaseQueryTableValue
	 */
	protected $_value = null;

	/**
	 * Set this to false if you don't want a column, subquery or function to be in your selected column list
	 *
	 * @var bool
	 */
	protected $_select = true;

	/**
	 * Set this to false if you don't want a column reference to automatically include a table alias prefix
	 *
	 * @var bool
	 */
	protected $_prefix = true;

	/**
	 * Used internally to determine if the column should be handled specially as it's part of a where clause
	 *
	 * @var bool
	 */
	protected $_where = false;

	/**
	 * Constructor
	 *
	 * @param string 	$name
	 * @param string 	$alias
	 * @param mixed 	$value
	 */
	public function __construct($name, $alias = null, $value = null)
	{
		$this->_name 	= $name;
		$this->_alias	= $alias;
		$this->_value 	= $value;
	}

	/**
	 * Checks to see if the current query type mandates a table alias prefix and generates it if so
	 *
	 * @param integer $type
	 * @return string
	 */
	protected function _getPrefixSQL($type)
	{
		static $required_list = array(
			DatabaseQuery::SELECT,
			DatabaseQuery::DELETE
		);

		$prefix = '';
		if(in_array($type, $required_list)) {
			$prefix = sprintf('%s.', $this->getTable()->getAlias());
		}

		return $prefix;
	}

	/**
	 * Determine if the column alias should be included if it's been supplied
	 *
	 * @param integer $type
	 * @return string
	 */
	protected function _getAliasSQL($type)
	{
		$alias = '';
		if($type == DatabaseQuery::SELECT &&
			!empty($this->_alias) &&
			$this->getAlias() !== $this->getName()) {
				$alias = sprintf(' AS %s', $this->getAlias());
		}

		return $alias;
	}

	/**
	 * Check if a query type needs the set SQL or the select SQL
	 *
	 * @param integer $type
	 * @return boolean
	 */
	protected function _isSetter($type)
	{
		return (in_array($type, array(DatabaseQuery::UPDATE, DatabaseQuery::INSERT)));
	}

	/**
	 * Get the SQL for referencing the column
	 *
	 * @param integer $type
	 * @return string
	 */
	public function getSQL($type = null)
	{
		if($this->_isSetter($type) && !$this->getWhere()) {
			return $this->_getSetSQL($type);
		} else {
			return $this->_getSelectSQL($type);
		}
	}

	/**
	 * Get the select query SQL for a column
	 *
	 * @param integer $type
	 * @return string
	 */
	private function _getSelectSQL($type)
	{
		$prefix = $this->_getPrefixSQL($type);
		$alias 	= $this->_getAliasSQL($type);

		return sprintf('%s%s%s', $prefix, $this->getName(), $alias);
	}

	/**
	 * Get the set SQL
	 *
	 * @param integer $type
	 * @return string
	 */
	private function _getSetSQL($type)
	{
		return sprintf('%s = %s', $this->getName(), $this->getValue()->getSQL($type));
	}
}