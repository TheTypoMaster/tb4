<?php

defined('_JEXEC') or die();

jimport('mobileactive.object.value');

/**
 * Order clause class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableOrder extends ValueObject
{
	/**
	 * Ascending order
	 *
	 * @var integer
	 */
	const ASCENDING = 0;

	/**
	 * Descending order
	 *
	 * @var integer
	 */
	const DESCENDING = 1;

	/**
	 * Owner table reference
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_table = null;

	/**
	 * Column object for the order clause
	 *
	 * @var DatabaseQueryTableColumn
	 */
	protected $_column;

	/**
	 * Direction flag
	 *
	 * @var integer
	 */
	protected $_direction;

	/**
	 * Constructor
	 *
	 * @param DatabaseQueryTableColumn $column
	 * @param integer $direction
	 */
	public function __construct(DatabaseQueryTableColumn $column, $direction = null)
	{
		if(is_null($direction)) {
			$direction = self::ASCENDING;
		}

		$this->_column 		= $column;
		$this->_direction 	= $direction;
	}

	/**
	 * Get the name of the currently assigned order direction
	 *
	 * @return string
	 */
	public function getDirectionName()
	{
		return ($this->_direction == self::ASCENDING) ? 'ASC' : 'DESC';
	}

	/**
	 * Get the SQL for a query
	 *
	 * @return string
	 */
	public function getSQL()
	{
		return sprintf('%s.%s %s',
						$this->getColumn()->getTable()->getAlias(),
						$this->getColumn()->getName(),
						$this->getDirectionName()
		);
	}
}