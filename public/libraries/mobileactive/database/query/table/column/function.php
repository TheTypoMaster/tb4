<?php

defined('_JEXEC') or die();

jimport('mobileactive.database.helper.function');
jimport('mobileactive.database.query.table.column');

/**
 * Database query column function class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableColumnFunction extends DatabaseQueryTableColumn
{
	/**
	 * The function type
	 *
	 * @var integer
	 */
	protected $_type = null;

	/**
	 * Whether or not to escape the value of this object before adding it to a query
	 *
	 * @var bool
	 */
	protected $_escape = false;

	/**
	 * Whether or not to prefix the column name before adding it to the query
	 *
	 * @var bool
	 */
	protected $_prefix = true;

	/**
	 * Constructor, type defaults to COUNT
	 *
	 * @param string 	$name
	 * @param string 	$alias
	 * @param mixed 	$value
	 * @param integer 	$type
	 */
	public function __construct($name, $alias = null, $value = null, $type = null)
	{
		if(is_null($type)) {
			$type = DatabaseQueryHelperFunction::COUNT;
		}

		$this->_name 	= $name;
		$this->_alias 	= $alias;
		$this->_value	= $value;
		$this->_type 	= $type;
	}

	/**
	 * Get the SQL for a column function
	 *
	 * @param integer $type
	 * @return string
	 */
	public function getSQL($type = null)
	{
		$prefix = '';
		if($this->getName() != '*') {
			$prefix = $this->_getPrefixSQL($type);
		}

		$alias 	= $this->_getAliasSQL($type);
		return sprintf('%s(%s%s)%s',
			DatabaseQueryHelperFunction::getFunctionName($this->_type),
			$prefix,
			$this->getName(),
			$alias
		);
	}
}