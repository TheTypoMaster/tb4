<?php

defined('_JEXEC') or die();

jimport('mobileactive.object.value');
jimport('mobileactive.database.query.table.join');
jimport('mobileactive.database.query.table.where');
jimport('mobileactive.database.query.table.group');
jimport('mobileactive.database.query.table.order');
jimport('mobileactive.database.query.table.column');
jimport('mobileactive.database.query.table.column.function');
jimport('mobileactive.database.query.table.column.subquery');
jimport('mobileactive.database.query.table.value');
jimport('mobileactive.database.query.table.value.column');
jimport('mobileactive.database.query.table.value.subquery');
jimport('mobileactive.database.query.table.value.function');

/**
 * Database query table
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryTable extends ValueObject
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Table alias (usually set by the query interface)
	 *
	 * @var string
	 */
	protected $_alias = null;

	/**
	 * Where object list
	 *
	 * @var array
	 */
	protected $_where_list = array();

	/**
	 * Join object list
	 *
	 * @var array
	 */
	protected $_join_list = array();

	/**
	 * Order object list
	 *
	 * @var array
	 */
	protected $_order_list = array();

	/**
	 * Group object list
	 *
	 * @var array
	 */
	protected $_group_list = array();

	/**
	 * Column object list
	 *
	 * @var array
	 */
	protected $_column_list = array();

	/**
	 * Subquery object list
	 *
	 * @var array
	 */
	protected $_subquery_list = array();

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->_name = $name;
	}
	
	public function __destruct()
	{
        $this->_where_list = array();
    	$this->_join_list = array();
    	$this->_order_list = array();
    	$this->_group_list = array();
    	$this->_column_list = array();
    	$this->_subquery_list = array();
	    $this->_name = null;
	    $this->_alias = null;
    }

	/**
	 * Attempt to retrieve a specific column object
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getColumnByName($name)
	{
		foreach($this->_column_list as $column) {
			if($name == $column->getName()) {
				return $column;
			}
		}

		return false;
	}

	/**
	 * Attempt to retrieve a specific join object
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getJoinTable($name)
	{
		foreach($this->_join_list as $join) {
			if($name == $join->getTable()->getName()) {
				return $join->getTable();
			}
		}

		return false;
	}

	/**
	 * Apply Darwinian principles to variables. Okay not really, but it's still a cool name.
	 * This method takes a mixed variable and turns it into a DB value object of your choice.
	 *
	 * @param mixed 	$variable
	 * @param string 	$type
	 * @return object
	 */
	private function _evolve($variable, $type)
	{
		if($variable instanceof $type === false) {
			$variable = new $type($variable);
		}

		return $variable;
	}

	/**
	 * Add a column to the table
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addColumn($column, $chain = true)
	{
		$column = $this->_evolve($column, 'DatabaseQueryTableColumn');
		$column->setTable($this);

		array_push($this->_column_list, $column);

		return $this->_chain($column, $chain);
	}

	/**
	 * Add a column to the table but not the list of fields being selected
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addReferenceColumn($column, $chain = true)
	{
		$column = $this->addColumn($column, false);
		$column->setSelect(false);

		return $this->_chain($column, $chain);
	}

	/**
	 * Add a column wrapped with a SQL function
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param integer 					$type
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addColumnFunction($column, $type = null, $chain = true)
	{
		$column = $this->_evolve($column, 'DatabaseQueryTableColumnFunction');
		if(is_null($type)) {
			$type = DatabaseQueryHelperFunction::COUNT;
		}

		$column->setType($type);
		$this->addColumn($column, $chain);
		
		return $this->_chain($column, $chain);
	}

	/**
	 * Add a subquery in place of a column
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addColumnSubquery($column, $chain = true)
	{
		$column = $this->_evolve($column, 'DatabaseQueryTableColumnSubquery');
		return $this->addColumn($column, $chain);
	}

	/**
	 * Add a where clause to the table
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param DatabaseQueryTableValue 	$value
	 * @param integer 					$context
	 * @param integer 					$operator
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addWhere($column, $value, $context = null, $operator = null, $chain = true)
	{
		$column = $this->_evolve($column, 'DatabaseQueryTableColumn');
		$value	= $this->_evolve($value, 'DatabaseQueryTableValue');
		
		$column->setValue($value);
		if($this->getColumnByName($column->getName()) != $column) {
			$this->addReferenceColumn($column);
		}

		$column->setWhere(true);

		$where = new DatabaseQueryTableWhere($column, $context, $operator);
		array_push($this->_where_list, $where);

		return $this->_chain($where, $chain);
	}
	
	/**
	* Add function where clause
	*
	* @param DatabaseQueryTableColumn $column
	* @param DatabaseQueryTableValueFunction $value
	* @param integer $context
	* @param integer $operator
	* @param boolean $chain
	* @return mixed
	*/
	public function addFunctionWhere($column, $value, $context = null, $operator = null, $chain = true)
	{
		$value	= $this->_evolve($value, 'DatabaseQueryTableValueFunction');
	
		return $this->addWhere($column, $value, $context, $operator, $chain);
	}

	/**
	 * Add a join
	 *
	 * @param DatabaseQueryTable 		$table
	 * @param DatabaseQueryTableColumn 	$column_left
	 * @param DatabaseQueryTableColumn 	$column_right
	 * @param integer 					$join_type
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addJoin($table, $column_left, $column_right, $join_type = null, $chain = true)
	{
		$table 		= $this->_evolve($table, 'DatabaseQueryTable');

		$column_1 	= $this->_evolve($column_left, 'DatabaseQueryTableColumn');
		$column_2	= $this->_evolve($column_right, 'DatabaseQueryTableColumn');

		if($this->getColumnByName($column_1->getName()) != $column_1) {
			$this->addReferenceColumn($column_1);
		}

		if($table->getColumnByName($column_2->getName()) != $column_2) {
			$table->addReferenceColumn($column_2);
		}

		$join = new DatabaseQueryTableJoin($table, $column_1, $column_2, $join_type);
		array_push($this->_join_list, $join);

		return $this->_chain($join, $chain);
	}

	/**
	 * Add a group clause to the table
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addGroup($column, $chain = true)
	{
		$column = $this->_evolve($column, 'DatabaseQueryTableColumn');
		if($this->getColumnByName($column->getName()) != $column) {
			$this->addReferenceColumn($column);
		}

		$group = new DatabaseQueryTableGroup($column);
		array_push($this->_group_list, $group);

		return $this->_chain($group, $chain);
	}

	/**
	 * Add an order clause to the table
	 *
	 * @param DatabaseQueryTableColumn 	$column
	 * @param integer 					$direction
	 * @param boolean 					$chain
	 * @return mixed
	 */
	public function addOrder($column, $direction = null, $chain = true)
	{
		$column = $this->_evolve($column, 'DatabaseQueryTableColumn');
		if($this->getColumnByName($column->getName()) != $column) {
			$this->addReferenceColumn($column);
		}

		$order = new DatabaseQueryTableOrder($column, $direction);
		array_push($this->_order_list, $order);

		return $this->_chain($order, $chain);
	}
}