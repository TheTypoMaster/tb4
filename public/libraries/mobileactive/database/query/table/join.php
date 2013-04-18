<?php

defined('_JEXEC') or die();

jimport('mobileactive.object.value');

/**
 * Database query join class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableJoin extends ValueObject
{
	/**
	 * Inner join
	 *
	 * @var integer
	 */
	const INNER = 0;

	/**
	 * Left join
	 *
	 * @var integer
	 */
	const LEFT	= 1;

	/**
	 * Right join
	 *
	 * @var integer
	 */
	const RIGHT = 2;

	/**
	 * Table object denoting the table to be joined
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_join_table = null;

	/**
	 * Table object denoting the parent table
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_table = null;

	/**
	 * Column object denoting the local column used in the join
	 *
	 * @var DatabaseQueryTableColumn
	 */
	protected $_column_self;

	/**
	 * Column object denoting the column in the joined table
	 *
	 * @var DatabaseQueryTableColumn
	 */
	protected $_column_join;

	/**
	 * Type of join
	 *
	 * @var integer
	 */
	protected $_type;

	/**
	 * Constructor
	 *
	 * @param DatabaseQueryTable $table
	 * @param DatabaseQueryTableColumn $column_self
	 * @param DatabaseQueryTableColumn $column_join
	 * @param integer $type
	 */
	public function __construct(DatabaseQueryTable $join_table, DatabaseQueryTableColumn $column_self, DatabaseQueryTableColumn $column_join, $type = null)
	{
		if(is_null($type)) {
			$type = self::INNER;
		}

		$this->_join_table	= $join_table;
		$this->_column_self = $column_self;
		$this->_column_join = $column_join;
		$this->_type		= $type;
	}

	/**
	 * Get the type name for the currently selected join type
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		$type = '';
		switch($this->_type) {
			case self::LEFT:
				$type = 'LEFT';
				break;
			case self::RIGHT:
				$type = 'RIGHT';
				break;
			case self::INNER:
			default:
				$type = 'INNER';
				break;
		}

		return $type;
	}

	/**
	 * Get the SQL for a query
	 *
	 * @return string
	 */
	public function getSQL()
	{
		return sprintf('%s JOIN %s AS %s ON %s.%s = %s.%s',
						$this->getTypeName(),
						$this->getJoinTable()->getName(),
						$this->getJoinTable()->getAlias(),
						$this->getColumnSelf()->getTable()->getAlias(),
						$this->getColumnSelf()->getName(),
						$this->getJoinTable()->getAlias(),
						$this->getColumnJoin()->getName()
		);
	}
}