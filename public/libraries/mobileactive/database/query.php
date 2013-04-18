<?php

defined('_JEXEC') or die();

jimport('joomla.database.database');
jimport('mobileactive.object.value');
jimport('mobileactive.database.query.table');
jimport('mobileactive.database.query.table.where');
jimport('mobileactive.database.query.table.join');
jimport('mobileactive.database.query.table.group');
jimport('mobileactive.database.query.table.order');
jimport('mobileactive.database.query.table.column');

/**
 * SQL query builder
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQuery extends ValueObject
{
	/**
	 * Database instance
	 *
	 * @var JDatabase
	 */
	protected $_db = null;

	/**
	 * Main table for the query
	 *
	 * @var DatabaseQueryTable
	 */
	protected $_table = null;

	/**
	 * Limit for select queries
	 *
	 * @var integer
	 */
	protected $_limit = null;

	/**
	 * Offset for select queries
	 *
	 * @var integer
	 */
	protected $_offset = null;

	/**
	 * The SQL query
	 *
	 * @var string
	 */
	protected $_query = '';

	/**
	 * Used alias pool for table aliases
	 *
	 * @var array
	 */
	protected $_table_alias_list = array();

	/**
	 * Used alias pool for column aliases
	 *
	 * @var array
	 */
	protected $_column_alias_list = array();

	/**
	 * Keeps track of whether WHERE has been added to the query's where list
	 *
	 * @var boolean
	 */
	protected $_where_context_added = false;

	/**
	 * SQL fragment list
	 *
	 * @var array
	 */
	private $_sql = array();

	/**
	 * SELECT query
	 *
	 * @var integer
	 */
	const SELECT = 0;

	/**
	 * INSERT query
	 *
	 * @var integer
	 */
	const INSERT = 1;

	/**
	 * UPDATE query
	 *
	 * @var integer
	 */
	const UPDATE = 2;

	/**
	 * DELETE query
	 *
	 * @var integer
	 */
	const DELETE = 3;

	/**
	 * Smart insert is an insert with a duplicate key update clause
	 *
	 * @var integer
	 */
	const SMART_INSERT = 4;

	/**
	 * This is a pseudo-type used to hint at special treatment for
	 * columns and values used within a where clause
	 *
	 * @var integer
	 */
	const WHERE = 5;

	/**
	 * Constructor
	 *
	 * @param DatabaseQueryTable 	$table
	 * @param integer 				$limit
	 */
	public function __construct(DatabaseQueryTable $table, $limit = null, $offset = null)
	{
		$this->setTable($table);

		$this->setLimit($limit);
		$this->setOffset($offset);

		$this->setDb();
	}

	/**
	 * Initialise a Joomla database connection for escaping values
	 */
	public function setDb()
	{
		$this->_db =& JFactory::getDBO();
	}

	/**
	 * Count the table list to determine after building if this is a single table query
	 *
	 * @return bool
	 */
	private function _isSingleTableQuery()
	{
		return (count($this->_table_list) == 1);
	}

	/**
	 * Add a string to the internal query
	 *
	 * @param string 	$string
	 * @param bool 		$trailing_space
	 */
	private function _addToQuery($string, $trailing_space = true)
	{
		$this->_query .= $string;
		if($trailing_space) {
			$this->_query .= ' ';
		}
	}

	/**
	 * Use the current object contents to create a select query
	 *
	 * @return string
	 */
	public function getSelect()
	{
		return $this->_getQuery(self::SELECT);
	}

	/**
	 * Use the current object contents to create an insert query
	 *
	 * @return string
	 */
	public function getInsert()
	{
		return $this->_getQuery(self::INSERT);
	}

	/**
	 * Use the current object contents to create an update query
	 *
	 * @return string
	 */
	public function getUpdate()
	{
		return $this->_getQuery(self::UPDATE);
	}

	/**
	 * Use the current object contents to create a delete query
	 *
	 * @return string
	 */
	public function getDelete()
	{
		return $this->_getQuery(self::DELETE);
	}

	/**
	 * Use the current object contents to create a smart insert query
	 *
	 * @return string
	 */
	public function getSmartInsert()
	{
		return $this->_getQuery(self::SMART_INSERT);
	}

	/**
	 * Initiate query building if required and return the SQL for the query
	 *
	 * @param integer $type
	 * @return string
	 */
	private function _getQuery($type = null)
	{
		if(is_null($type)) {
			$type = self::SELECT;
		}

		if(empty($this->_query)) {
			$this->_buildQuery($type);
		}

		return $this->_query;
	}

	/**
	 * Build the SQL query
	 *
	 * @param integer $type
	 * @return string
	 */
	private function _buildQuery($type)
	{
		$table = $this->getTable();

		$this->_prepareTableList($table);
		$this->_prepareSubqueryList($table);

		switch($type) {
			case self::INSERT:
				$this->_buildInsert($table);
				break;
			case self::SMART_INSERT:
				$this->_buildSmartInsert($table);
				break;
			case self::UPDATE:
				$this->_buildUpdate($table);
				break;
			case self::DELETE:
				$this->_buildDelete($table);
				break;
			case self::SELECT:
			default:
				$this->_buildSelect($table);
				break;
		}
	}

	private function _prepareSubqueryList(DatabaseQueryTable $table)
	{
		$subquery_list = $table->getSubqueryList();
		foreach($subquery_list as $subquery) {
			$subquery->setReservedAliasList($this->exportTableAliasList());
		}
	}

	/**
	 * Build a select query
	 *
	 * @param DatabaseQueryTable $table
	 */
	private function _buildSelect(DatabaseQueryTable $table)
	{
		$this->_addToQuery('SELECT');

		$column_list = $this->getTable()->getColumnList();

		$select = false;
		foreach($column_list as $column) {
			if(!$column->getSelect()) {
				continue;
			}

			$select = true;
		}

		if(!$select) {
			$this->getTable()->addColumn('*');
		}

		$this->_addToQuery($this->_generateColumnSQL($table, self::SELECT));

		$name = $table->getName();
		if(preg_match('/[a-z]/i', $name)) {
			$this->_addToQuery(sprintf('FROM %s AS %s', $table->getName(), $table->getAlias()));
		}

		$join_sql = $this->_generateJoinSQL($table);
		if(!empty($join_sql)) {
			$this->_addToQuery($join_sql);
		}

		$where_sql = $this->_generateWhereSQL($table, self::SELECT);
		if(!empty($where_sql)) {
			$this->_addToQuery($where_sql);
		}

		$group_sql = $this->_generateGroupSQL($table);
		if(!empty($group_sql)) {
			$this->_addToQuery('GROUP BY');
			$this->_addToQuery($group_sql);
		}

		$order_sql = $this->_generateOrderSQL($table);
		if(!empty($order_sql)) {
			$this->_addToQuery('ORDER BY');
			$this->_addToQuery($order_sql);
		}

		if(!empty($this->_limit)) {
			if(is_null($this->_offset)) {
				$this->_offset = 0;
			}

			$this->_addToQuery(sprintf('LIMIT %d, %d', $this->_offset, $this->_limit));
		}
	}

	/**
	 * Build an insert query
	 *
	 * @param DatabaseQueryTable $table
	 */
	private function _buildInsert(DatabaseQueryTable $table)
	{
		$this->_addToQuery(sprintf('INSERT INTO %s SET', $table->getName()));
		$this->_addToQuery($this->_generateColumnSQL($table, self::INSERT));
	}

	/**
	 * Build an update query
	 *
	 * @param DatabaseQueryTable $table
	 */
	private function _buildUpdate(DatabaseQueryTable $table, $smart = false)
	{
		$table_alias = $table->getAlias();

		$this->_addToQuery('UPDATE');
		if(!$smart) {
			$this->_addToQuery(sprintf('%s SET', $table->getName(), $table_alias));
		}

		$this->_addToQuery($this->_generateColumnSQL($table, self::UPDATE));
		$where_sql = $this->_generateWhereSQL($table, self::UPDATE);

		if(!empty($where_sql)) {
			$this->_addToQuery($where_sql);
		}
	}

	/**
	 * Build an insert query with a duplicate key update condition
	 *
	 * @param DatabaseQueryTable $table
	 */
	private function _buildSmartInsert($table)
	{
		$query = '';

		$insert_query = clone($this);
		$update_query = clone($this);

		$insert_query->_buildInsert($table);

		$this->_addToQuery($insert_query->getQuery());
		$this->_addToQuery('ON DUPLICATE KEY');

		$update_query->_buildUpdate($table, true);
		$this->_addToQuery($update_query->getQuery());
	}

	/**
	 * Build a delete query
	 *
	 * @param DatabaseQueryTable $table
	 */
	private function _buildDelete(DatabaseQueryTable $table)
	{
		$this->_addToQuery(sprintf('DELETE %s FROM %s AS %s',
								$table->getAlias(),
								$table->getName(),
								$table->getAlias()
		));

		$join_sql = $this->_generateJoinSQL($table);
		if(!empty($join_sql)) {
			$this->_addToQuery($join_sql);
		}

		$where_sql = $this->_generateWhereSQL($table, self::DELETE);
		if(!empty($where_sql)) {
			$this->_addToQuery($where_sql);
		}

		$group_sql = $this->_generateGroupSQL($table);
		if(!empty($group_sql)) {
			$this->_addToQuery('GROUP BY');
			$this->_addToQuery($group_sql);
		}

		$order_sql = $this->_generateOrderSQL($table);
		if(!empty($order_sql)) {
			$this->_addToQuery('ORDER BY');
			$this->_addToQuery($order_sql);
		}

		if(!empty($this->_limit)) {
			if(empty($this->_offset)) {
				$this->_offset = 0;
			}

			$this->_addToQuery(sprintf('LIMIT %d, %d', $this->_offset, $this->_limit));
		}
	}

	/**
	 * Generate a table alias which doesn't conflict with existing aliases
	 *
	 * @param DatabaseQueryTable $table
	 * @return string
	 */
	private function _generateTableAlias(DatabaseQueryTable $table)
	{
		static $base_list = array();

		if(empty($this->_table_alias_list)) {
			$base_list = array();
		}

		$table_name = $table->getName();
		if(!array_key_exists($table_name, $base_list)) {
			$base 	= str_replace('#__', '', $table_name);
			$alias 	= '';

			for($x = 0; $x < strlen($base); ++$x) {
				if($x == 0) {
					$alias .= $base{$x};
				}

				if($base{$x} == '_') {
					$alias .= $base{$x + 1};
				}
			}

			$base_list[$table_name] = $alias;
		}

		$final_alias = $base_alias = $base_list[$table_name];
		$counter = 0;

		while(in_array($final_alias, $this->_table_alias_list)) {
			++$counter;
			$final_alias = $base_alias . $counter;
		}

		$this->_table_alias_list[] = $final_alias;
		return $final_alias;
	}

	/**
	 * Generate a column alias which doesn't conflict with others
	 *
	 * @param DatabaseQueryTableColumn $column
	 * @return string
	 */
	private function _generateColumnAlias(DatabaseQueryTableColumn $column)
	{
		$alias = $column->getAlias();
		if(empty($alias)) {
			$alias = $name = $column->getName();
		}

		if(in_array($alias, $this->_column_alias_list)) {
			$table_name = $column->getTable()->getName();

			$alias = $base_alias = str_replace('#__', '', $table_name) . '_' . $name;
			$counter = 0;

			while(in_array($alias, $this->_column_alias_list)) {
				++$counter;
				$alias = $base_alias . '_' . $counter;
			}
		}

		$this->_column_alias_list[] = $alias;
		return $alias;
	}

	/**
	 * Extract the value object list from the main table and build the query heirarchy
	 *
	 * @param DatabaseQueryTable $table
	 * @return void
	 */
	private function _prepareTableList(DatabaseQueryTable $table)
	{
		$table->setAlias($this->_generateTableAlias($table));
		$this->_prepareColumnList($table);

		$table->setName($this->_nameQuote($table->getName()));
		$join_list = $table->getJoinList();

		foreach($join_list as $join) {
			$join_table = $join->getJoinTable();
			$this->_prepareTableList($join_table);
		}
	}

	/**
	 * Extract the column value object list for a table and prepare values
	 *
	 * @param DatabaseQueryTable $table
	 * @return void
	 */
	private function _prepareColumnList(DatabaseQueryTable $table)
	{
		$column_list = $table->getColumnList();
		foreach($column_list as $column) {
			$this->_prepareColumn($column);
		}
	}

	private function _prepareColumn(DatabaseQueryTableColumn $column)
	{
		$column->setValue($this->_prepareColumnValue($column->getValue()));
	}

	/**
	 * Prepare a value for use in a query
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	private function _prepareColumnValue($value)
	{
		if(is_object($value)) {
			if($value instanceof DatabaseQueryTableValue) {
				$value->setValue($this->_prepareColumnValue($value->getValue()));
			}

			return $value;
		}

		if(is_array($value)) {
			foreach($value as &$v) {
				$v = $this->_prepareValue($v);
			}

			return $value;
		}

		if(is_null($value)) {
			return $this->_prepareNull($value);
		}

		if(is_int($value)) {
			return $this->_prepareInteger($value);
		}

		if(is_float($value)) {
			return $this->_prepareFloat($value);
		}

		return $this->_prepareString($value);
	}

	/**
	 * Escape a string for use in a database query
	 *
	 * @param string $value
	 * @return string
	 */
	private function _prepareString($value)
	{
		return $this->_quote($value);
	}

	/**
	 * Prepare an integer for use in a database query
	 *
	 * @param integer $value
	 * @return integer
	 */
	private function _prepareInteger($value)
	{
		return (int)preg_replace('/[^0-9-]/', '', $value);
	}

	/**
	 * Prepare a float for use in a database query
	 *
	 * @param float $value
	 * @return float
	 */
	private function _prepareFloat($value)
	{
		return (float)preg_replace('/[^0-9\.]/', '', $value);
	}

	/**
	 * Prepare a NULL
	 *
	 * @param null $value
	 * @return string
	 */
	private function _prepareNull($value)
	{
		return 'NULL';
	}

	/**
	 * Use JDatabase to escape a string
	 *
	 * @param string $string
	 * @return string
	 */
	private function _quote($string)
	{
		return $this->_db->quote($string);
	}

	/**
	 * Use JDatabase to quote a table/column name
	 *
	 * @param string $string
	 * @return string
	 */
	private function _nameQuote($string)
	{
		return $this->_db->nameQuote($string);
	}

	/**
	 * Loops through value objects for a prepared table and generate their output SQL
	 *
	 * @param DatabaseQueryTable 	$table
	 * @param string 				$list_method
	 * @param string 				$separator
	 * @param integer 				$type
	 * @return string
	 */
	private function _generateSQL($table, $list_method, $separator = null, $type = null)
	{
		if(is_null($separator)) {
			$separator = ' ';
		}

		if(is_null($type)) {
			$type = self::SELECT;
		}

		if(!array_key_exists($list_method, $this->_sql)) {
			$this->_sql[$list_method] = array();
		}

		$item_list = $table->$list_method();
		foreach($item_list as $item) {
			if($item instanceof DatabaseQueryTableColumn && $item->getSelect() === false) {
				continue;
			}

			$item_sql = $item->getSQL($type);
			if(!empty($item_sql)) {
				$this->_sql[$list_method][] = $item_sql;
			}
		}

		$join_list = $table->getJoinList();
		foreach($join_list as $join) {
			$join_table = $join->getJoinTable();
			$this->_generateSQL($join_table, $list_method, $separator, $type);
		}

		return implode($separator, $this->_sql[$list_method]);
	}

	/**
	 * Uses _generateSQL() to generate the column SQL
	 *
	 * @param DatabaseQueryTable $table
	 * @param unknown_type $type
	 */
	private function _generateColumnSQL(DatabaseQueryTable $table, $type = null)
	{
		return $this->_generateSQL($table, 'getColumnList', ', ', $type);
	}

	/**
	 * Generate the where SQL
	 *
	 * @param DatabaseQueryTable 	$table
	 * @param integer 				$type
	 * @return string
	 */
	private function _generateWhereSQL(DatabaseQueryTable $table, $type)
	{
		if(!array_key_exists('getWhereList', $this->_sql)) {
			$this->_sql['getWhereList'] = array();
		}

		$item_list = $table->getWhereList();
		foreach($item_list as $item) {
			$this->_sql['getWhereList'][] = $item->getSQL($type, $this->_where_context_added);
			if(!$this->_where_context_added) {
				$this->_where_context_added = true;
			}
		}

		$join_list = $table->getJoinList();
		foreach($join_list as $join) {
			$join_table = $join->getJoinTable();
			$this->_generateWhereSQL($join_table, $type);
		}

		return implode(' ', $this->_sql['getWhereList']);
	}

	/**
	 * Uses _generateSQL() to generate the join clauses
	 *
	 * @param DatabaseQueryTable $table
	 * @return string
	 */
	private function _generateJoinSQL(DatabaseQueryTable $table)
	{
		return $this->_generateSQL($table, 'getJoinList');
	}

	/**
	 * Uses _generateSQL() to generate the order clauses
	 *
	 * @param DatabaseQueryTable $table
	 * @return string
	 */
	private function _generateOrderSQL(DatabaseQueryTable $table)
	{
		return $this->_generateSQL($table, 'getOrderList', ', ');
	}

	/**
	 * Uses _generateSQL() to generate the group clauses
	 *
	 * @param DatabaseQueryTable $table
	 * @return string
	 */
	private function _generateGroupSQL(DatabaseQueryTable $table)
	{
		return $this->_generateSQL($table, 'getGroupList', ', ');
	}

	/**
	 * Export the internal table alias list.
	 *
	 * @return array
	 */
	public function exportTableAliasList()
	{
		return $this->_table_alias_list;
	}

	/**
	 * Import the alias list from a different query. This is used so that complex subqueries
	 * will generate their own aliases even when addressing the same tables.
	 *
	 * @param array $alias_list
	 */
	public function importTableAliasList(array $alias_list)
	{
		$this->_table_alias_list += $alias_list;
	}

	/**
	 * Use a regex to determine if a passed string is actually a SQL function
	 *
	 * @param string $value
	 * @deprecated There are proper value objects for SQL functions and subqueries now
	 * @return bool
	 */
	private function _isSQLFunction($value)
	{
		return (preg_match('/^[a-z]+\(.*\)$/i', $value) || preg_match('/^\(SELECT/i', $value));
	}
}