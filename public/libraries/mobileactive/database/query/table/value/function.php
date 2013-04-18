<?php

defined('_JEXEC') or die();

jimport('mobileactive.database.query.table.value');

/**
 * Value function class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTableValueFunction extends DatabaseQueryTableValue
{
	/**
	 * The funciton type
	 *
	 * @var integer
	 */
	protected $_type;

	/**
	 * Whether to escape the value or not
	 *
	 * @var bool
	 */
	protected $_escape = false;

	/**
	 * Constructor
	 *
	 * @param integer 	$type
	 * @param mixed 	$value
	 */
	public function __construct($type = null, $value = '')
	{
		if(is_null($type)) {
			$type = DatabaseQueryHelperFunction::COUNT;
		}

		$this->_type = $type;
		parent::__construct($value, false);
	}

	/**
	 * Return the SQL used to represent the function call
	 *
	 * @param integer $type
	 * @return string
	 */
	public function getSQL($type = null)
	{
		return sprintf('%s(%s)',
			DatabaseQueryHelperFunction::getFunctionName($this->_type),
			(preg_match('/[^\'\`]+/', $this->_value)) ? $this->_value : ''
		);
	}
}