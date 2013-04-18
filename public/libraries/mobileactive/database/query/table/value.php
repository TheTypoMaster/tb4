<?php
defined('_JEXEC') or die();

jimport('mobileactive.object.value');

class DatabaseQueryTableValue extends ValueObject
{
	/**
	 * The column which contains this value
	 *
	 * @var DatabaseQueryTableColumn
	 */
	protected $_column;

	/**
	 * The literal value of this object
	 *
	 * @var mixed
	 */
	protected $_value;

	/**
	 * Flags whether this value needs to be escaped before using it in a query
	 *
	 * @var boolean
	 */
	protected $_escape = true;

	/**
	 * Constructor
	 *
	 * @param mixed 	$value
	 * @param boolean 	$escape
	 */
	public function __construct($value, $escape = true)
	{
		$this->_value 	= $value;
		$this->_escape 	= $escape;
	}

	/**
	 * Generate the SQL for the value
	 *
	 * @return string
	 */
	public function getSQL()
	{
		return sprintf('%s', $this->_value);
	}
}