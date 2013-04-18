<?php

defined('_JEXEC') or die();

jimport('mobileactive.object.value');

/**
 * Where clause class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableWhere extends ValueObject
{
	/**
	 * Owner table reference
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_table = null;

	/**
	 * Column object which is the subject of the where condition
	 *
	 * @var DatabaseQueryTableColumn
	 */
	protected $_column;

	/**
	 * The context of the where clause; i.e. AND/OR
	 *
	 * @var integer
	 */
	protected $_context;

	/**
	 * The operator used for the where condition
	 *
	 * @var integer
	 */
	protected $_operator;

	/**
	 * AND context
	 *
	 * @var integer
	 */
	const CONTEXT_AND = 0;

	/**
	 * OR context
	 *
	 * @var integer
	 */
	const CONTEXT_OR = 1;

	/**
	 * Equal to operator
	 *
	 * @var integer
	 */
	const OPERATOR_EQUAL = 0;

	/**
	 * Not equal to operator
	 *
	 * @var integer
	 */
	const OPERATOR_NOT_EQUAL = 1;

	/**
	 * Greater than operator
	 *
	 * @var integer
	 */
	const OPERATOR_GREATER_THAN = 2;

	/**
	 * Less than operator
	 *
	 * @var integer
	 */
	const OPERATOR_LESS_THAN = 3;

	/**
	 * Greater than or equal to operator
	 *
	 * @var integer
	 */
	const OPERATOR_GREATER_OR_EQUAL = 4;

	/**
	 * Less than or equal to operator
	 *
	 * @var integer
	 */
	const OPERATOR_LESS_OR_EQUAL = 5;

	/**
	 * Greater or less than operator
	 *
	 * @var integer
	 */
	const OPERATOR_GREATER_OR_LESS_THAN = 6;

	/**
	 * IS NULL operator
	 *
	 * @var integer
	 */
	const OPERATOR_IS_NULL = 7;

	/**
	 * IS NOT NULL operator
	 *
	 * @var integer
	 */
	const OPERATOR_IS_NOT_NULL = 8;

	/**
	 * No operator. This is used for functions and the like.
	 *
	 * @var integer
	 */
	const OPERATOR_EMPTY = 9;

	/**
	 * Constructor
	 *
	 * @param DatabaseQueryTableColumn $column
	 * @param integer $context
	 * @param integer $operator
	 */
	public function __construct(DatabaseQueryTableColumn $column, $context = null, $operator = null)
	{
		if(is_null($context)) {
			$context = self::CONTEXT_AND;
		}

		if(is_null($operator)) {
			$operator = self::OPERATOR_EQUAL;
		}

		$this->_column 		= $column;
		$this->_context 	= $context;
		$this->_operator 	= $operator;
	}

	/**
	 * Get the name of the currently assigned context
	 *
	 * @return string
	 */
	public function getContextName()
	{
		return ($this->_context == self::CONTEXT_AND) ? 'AND' : 'OR';
	}

	/**
	 * Get the string operator for the currently assigned operator type
	 *
	 * @return string
	 */
	public function getOperatorString()
	{
		$operator = '';
		switch($this->_operator) {
			case self::OPERATOR_EQUAL:
				$operator = '=';
				break;
			case self::OPERATOR_NOT_EQUAL:
				$operator = '!=';
				break;
			case self::OPERATOR_GREATER_THAN:
				$operator = '>';
				break;
			case self::OPERATOR_LESS_THAN:
				$operator = '<';
				break;
			case self::OPERATOR_GREATER_OR_EQUAL:
				$operator = '>=';
				break;
			case self::OPERATOR_LESS_OR_EQUAL:
				$operator = '<=';
				break;
			case self::OPERATOR_GREATER_OR_LESS_THAN:
				$operator = '<>';
				break;
			case self::OPERATOR_IS_NULL:
				$operator = 'IS NULL';
				break;
			case self::OPERATOR_IS_NOT_NULL:
				$operator = 'IS NOT NULL';
				break;
			case self::OPERATOR_EMPTY:
				$operator = '';
				break;
		}

		return $operator;
	}

	/**
	 * Checks an operator to see if it implies it's own value (i.e. NULL or IS NULL)
	 *
	 * @param integer $operator
	 * @return bool
	 */
	private function _isEmptyValueOperator($operator)
	{
		static $empty_list = array(
			self::OPERATOR_IS_NULL,
			self::OPERATOR_IS_NOT_NULL
		);

		return in_array($operator, $empty_list);
	}

	/**
	 * Get the SQL for a query
	 *
	 * @param string 	$type
	 * @param boolean 	$include_context
	 * @return string
	 */
	public function getSQL($type, $include_context = true)
	{
		$prefix = ($include_context) ? $this->getContextName() : 'WHERE';
		$value 	= ($this->_isEmptyValueOperator($this->getOperator())) ? '' : $this->getColumn()->getValue()->getSQL($type);

		return sprintf('%s %s %s %s',
						$prefix,
						$this->getColumn()->getSQL($type),
						$this->getOperatorString(),
						$value
		);
	}
}