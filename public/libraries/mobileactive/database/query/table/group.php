<?php

defined('_JEXEC') or die();

jimport('mobileactive.object.value');

/**
 * Database query group class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableGroup extends ValueObject
{
	/**
	 * Owner table reference
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_table = null;

	/**
	 * Column to group by
	 *
	 * @var string
	 */
	protected $_column;

	/**
	 * Constructor
	 *
	 * @param DatabaseQueryTableColumn $column
	 */
	public function __construct(DatabaseQueryTableColumn $column)
	{
		$this->_column = $column;
	}

	/**
	 * Get the SQL for the group clause
	 *
	 * @return string
	 */
	public function getSQL()
	{
		return sprintf('%s.%s', $this->getColumn()->getTable()->getAlias(), $this->getColumn()->getName());
	}
}