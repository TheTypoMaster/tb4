<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('mobileactive.database.query');

/**
 * SuperModel - an attempt to make models in Joomla! which don't suck
 *
 * @author declan.kennedy
 * @package mobileactive
 */
abstract class SuperModel extends JModel
{
	/**
	 * The name of the table to which this model maps
	 *
	 * @var string
	 */
	protected $_table_name = null;

	/**
	 * The definitions for the fields which this model uses
	 *
	 * @var array
	 */
	protected $_member_list = array();

	/**
	 * List of errors
	 *
	 * @var array
	 */
	private $_error_list = array();

	/**
	 * Private storage for members
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Internal change log containing previous values of members which have
	 * been set since the object was instantiated.
	 *
	 * @var array
	 */
	private $_change_log = array();
	
	/**
	 * Simulation mode, does not actually execute SQL
	 * 
	 * @var boolean
	 */
	private $_simulation_mode = false;

	/**
	 * Value which is used to make up for PHP lacking a proper undef type
	 *
	 * @var string
	 */
	const UNDEFINED = '__UNDEFINED__';

	/**
	 * Error type corresponding to an error filed against the model itself
	 *
	 * @var string
	 */
	const ERROR_MEMBER_MODEL = '__MODEL__';

	/**
	 * Integer type
	 *
	 * @var integer
	 */
	const TYPE_INTEGER = 0;

	/**
	 * String type
	 *
	 * @var integer
	 */
	const TYPE_STRING = 1;

	/**
	 * Float type
	 *
	 * @var integer
	 */
	const TYPE_FLOAT = 2;

	/**
	 * Date/time type
	 *
	 * @var integer
	 */
	const TYPE_DATETIME = 3;

	/**
	 * Date/time type which should be set on insert
	 *
	 * @var integer
	 */
	const TYPE_DATETIME_CREATED = 4;

	/**
	 * Date/time type which should be set on update
	 *
	 * @var integer
	 */
	const TYPE_DATETIME_UPDATED = 5;

	/**
	 * Foreign key value used to load a dependent SM
	 *
	 * @var integer
	 */
	const TYPE_SUPERMODEL = 6;

	/**
	 * Single item finder
	 *
	 * @var integer
	 */
	const FINDER_SINGLE = 0;

	/**
	 * List finder (default)
	 *
	 * @var integer
	 */
	const FINDER_LIST = 1;

	/**
	 * Constructor. If a string, integer or associative array matching the defined primary key (for composite values) is provided
	 * the model will attempt to load the corresponding record. If an array which doesn't match the primary key fields is passed
	 * then 'set' mode is assumed.
	 *
	 * @param mixed $init
	 * @param array $config Optional config array to pass to JModel
	 */
	final public function __construct($init = array(), $config = array())
	{
		parent::__construct($config);
		$this->_inheritMemberList($this);

		if(!empty($init)) {
			if(is_array($init) && !$this->_isPrimary($init)) {
				$this->_setMembers($init);
			} else {
				$this->load($init);
			}
		}
	}

	/**
	 * Recursively merge parent member lists together to create a master list for the resulting child
	 */
	final protected function _inheritMemberList($object)
	{
		$self 	= get_class($object);
		$parent = get_parent_class($object);

		if($parent != 'SuperModel' && $self != 'SuperModel') {
			$instance = new $parent;
			$this->_member_list = array_merge($instance->getFullMemberList(), $this->_member_list);
			$this->_inheritMemberList($instance);
		}
	}

	/**
	 * Checks a data array to see if the keys correspond to the current composite primary key
	 *
	 * @param array $key
	 * @return bool
	 */
	private function _isPrimary($key)
	{
		$primary 	= $this->getPrimaryKey();
		$key_list 	= array_keys($key);

		sort($primary);
		sort($key_list);

		return ($primary === $key);
	}

	/**
	 * Checks to see if the primary key has a value. N.B.: for composite keys this means that
	 * ALL members will need a value.
	 *
	 * @return bool
	 */
	private function _isPrimarySet()
	{
		$primary = $this->getPrimaryKey();
		if(empty($primary)) {
			return false;
		}

		$set = true;
		foreach($primary as $key) {
			if($this->$key === self::UNDEFINED || empty($this->$key)) {
				$set = false;
			}
		}

		return $set;
	}

	/**
	 * Check whether the specified member name is classed as metadata
	 *
	 * @param string $name
	 * @return bool
	 */
	protected function _isMetadata($name)
	{
		$member_name = ($name instanceof DatabaseQueryTableColumn) ? $name->getName() : $name;

		if(!array_key_exists($member_name, $this->_member_list)) {
			return false;
		}

		return (array_key_exists('metadata', $this->_member_list[$member_name])
				&& $this->_member_list[$member_name]['metadata']);
	}
	
	/**
	 * Check if in simulation mode
	 */
	protected function _isSimulation(){
		return $this->_simulation_mode;
	}

	/**
	 * Magic getter
	 *
	 * @param string $name
	 * @return mixed
	 */
	final public function __get($name)
	{
		return (isset($this->_data[$name])) ? $this->_data[$name] : self::UNDEFINED;
	}

	/**
	 * Magic setter
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	final public function __set($name, $value)
	{
		$current = $this->__get($name);

		if($current !== $value && $current !== self::UNDEFINED) {
			$this->_addChange($name, $current);
		}

		$this->_data[$name] = $value;
	}

	/**
	 * Magic isset
	 *
	 * @param string $name
	 * @return bool
	 */
	final public function __isset($name)
	{
		return !($this->__get($name) === self::UNDEFINED);
	}

	/**
	 * Check if the object has changed since instantiation
	 *
	 * @return bool
	 */
	public function isChanged()
	{
		return !(empty($this->_change_log));
	}

	/**
	 * Add a change to the internal change log
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	private function _addChange($name, $value)
	{
		$this->_change_log[$name] = $value;
	}

	/**
	 * Get the internal changelog which tracks changes since instantiation
	 *
	 * @return array
	 */
	public function getChangeLog()
	{
		return $this->_change_log;
	}

	/**
	 * Take an array or object and set the provided values into the object, casting as required. Actual member values
	 * for the model will be type chacked and cast, other members will be assumed transient and will be stored as is.
	 *
	 * @param array $value_list
	 * @param array $mapping_list
	 */
	private function _setMembers($value_list, $mapping_list = array())
	{
		if(is_object($value_list)) {
			$value_list = (array)$value_list;
		}

		foreach($value_list as $member => $value) {
			$key = (array_key_exists($member, $mapping_list)) ? $mapping_list[$member] : $member;

			if(array_key_exists($key, $this->_member_list)) {
				$parameter_list = $this->_member_list[$key];

				switch($parameter_list['type']) {
					case self::TYPE_SUPERMODEL:
						$class = false;
						if(array_key_exists('model', $parameter_list)) {
							list($prefix, $type) = explode('model', strtolower($parameter_list['model']));
							$class =& JModel::getInstance($type, $prefix . 'Model', $value);
						}

						$value = ($class === false) ? $value : $class;
						break;
					case self::TYPE_INTEGER:
						$value = trim($value) === '' ? NULL : (int)$value;
						break;
					case self::TYPE_FLOAT:
						$value = trim($value) === '' ? NULL : (float)$value;
						break;
					case self::TYPE_STRING:
					default:
						$value = (string)$value;
						break;
				}
			}

			$this->$key = $value;
		}
	}

	/**
	 * Public interface for _setMembers
	 *
	 * @param array $value_list
	 * @param array $mapping_list
	 * @return void
	 */
	final public function setMembers(array $value_list, array $mapping_list = array())
	{
		$this->_setMembers($value_list, $mapping_list);
		return $this;
	}

	/**
	 * Get the primary key(s).
	 *
	 * @return array
	 */
	final public function getPrimaryKey()
	{
		static $primary = array();

		if(empty($primary)) {
			foreach($this->_member_list as $member => $parameter_list) {
				if(array_key_exists('primary', $parameter_list) && $parameter_list['primary']) {
					$primary[] = $member;
				}
			}
		}

		return $primary;
	}

	/**
	 * Set the primary key value
	 *
	 * @param mixed $value
	 */
	final public function setPrimaryKeyValue($value)
	{
		$key_list = $this->getPrimaryKey();

		foreach($key_list as $key) {
			$this->$key = (is_array($value)) ? $value[$key] : $value;
		}
	}

	/**
	 * Get the current value of the primary key
	 *
	 * @return mixed
	 */
	final public function getPrimaryKeyValue()
	{
		$key_list = $this->getPrimaryKey();

		$value = array();
		foreach($key_list as $key) {
			$value[] = $this->$key;
		}

		if(count($value) == 1) {
			$value = $value[0];
		}

		return $value;
	}

	/**
	 * Return the entire internal member list including parameters
	 *
	 * return array
	 */
	final public function getFullMemberList()
	{
		return $this->_member_list;
	}

	/**
	 * Return the internal member list
	 *
	 * @return array
	 */
	final public function getMemberList()
	{
		return array_fill_keys(array_keys($this->_member_list), null);
	}

	/**
	 * Return the name of the metadata table which corresponds to this model
	 *
	 * @return string
	 */
	private function _getMetadataTableName()
	{
		return $this->_table_name . '_metadata';
	}

	/**
	 * Guess the name of the foreign key for the current metadata table
	 *
	 * @return string
	 */
	private function _getMetadataForeignKeyName()
	{
		return preg_replace('/^#__/', '', $this->_table_name) . '_id';
	}

	/**
	 * Get a list of model members marked as metadata
	 *
	 * @return array
	 */
	public function getMetadataMemberList()
	{
		$metadata_list = array();
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('metadata', $parameter_list) && $parameter_list['metadata']) {
				$metadata_list[] = $member;
			}
		}

		return $metadata_list;
	}

	/**
	 * Get a table for a select query with the metadata table already added if required.
	 *
	 * @return DatabaseQueryTable
	 */
	protected function _getTable()
	{
		$table = new DatabaseQueryTable($this->_table_name);

		$metadata_list = array();
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('metadata', $parameter_list) && $parameter_list['metadata']) {
				$metadata_list[] = $member;
			} else {
				$table->addColumn($member);
			}
		}

		if(!empty($metadata_list)) {
			$table_name 	= $this->_getMetadataTableName();
			$foreign_key 	= $this->_getMetadataForeignKeyName();

			foreach($metadata_list as $member) {
				$metadata_table = new DatabaseQueryTable($table_name);
				$metadata_table->addColumn(new DatabaseQueryTableColumn('value', $member));
				$metadata_table->addWhere('name', $member);

				$table->addJoin(
					$metadata_table,
					'id',
					$foreign_key,
					DatabaseQueryTableJoin::LEFT
				);
			}

			$table->addGroup('id');
		}

		return $table;
	}

	/**
	 * Return a table object for a SuperModel
	 *
	 * @param string $model_name
	 * @param string $prefix
	 * @return SuperModel
	 */
	public static function getTableObject($model_name, $prefix)
	{
		$model =& JModel::getInstance($model_name, $prefix);

		$table = null;
		if(!is_null($model)) {
			$table = $model->_getTable();
		}

		return $table;
	}

	/**
	 * Find objects by criteria
	 *
	 * @param array $criteria_list
	 * @return mixed
	 */
	final public function find($criteria_list = array(), $type = null, $list_key = null)
	{
		if(is_null($type)) {
			$type = self::FINDER_LIST;
		}

		$db_method = ($type == self::FINDER_SINGLE) ? 'loadObject' : 'loadObjectList';
		$sm_method = ($type == self::FINDER_SINGLE) ? '_loadModel' : '_loadModelList';
		
		foreach($criteria_list as &$criteria) {
			$name = ($criteria['name'] instanceof DatabaseQueryTableColumn) ? $criteria['name']->getName() : $criteria['name'];

			if(!array_key_exists($name, $this->_member_list)) {
				throw new Exception("The member {$name} was not found in this model");
			}

			if(!array_key_exists('value', $criteria)) {
				$criteria['value'] = null;
			}

			if(!array_key_exists('context', $criteria) || is_null($criteria['context'])) {
				$criteria['context'] = DatabaseQueryTableWhere::CONTEXT_AND;
			}

			if(!array_key_exists('operator', $criteria) || is_null($criteria['operator'])) {
				$criteria['operator'] = DatabaseQueryTableWhere::OPERATOR_EQUAL;
			}
		}
	
		$table 	= $this->_find($criteria_list);
		$db 	=& JFactory::getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());
		
		$table->__destruct();
		unset($table);

		return $this->$sm_method($db->$db_method($list_key));
	}

	/**
	 * Turn a stdClass object containing data into a full model object
	 *
	 * @param object $data
	 * @return self
	 */
	protected function _loadModel($data)
	{
		if(is_null($data)) {
			return $data;
		}

		$class = get_class($this);
		return new $class((array)$data);
	}

	/**
	 * Turn an array of stdClass objects into an array of full models
	 *
	 * @param array $data_list
	 * @return array
	 */
	protected function _loadModelList($data_list)
	{
		if(!is_null($data_list)) {
			foreach($data_list as &$data) {
				$data = $this->_loadModel($data);
			}
		}

		return $data_list;
	}

	/**
	 * Find helper which adds the criteria to the table or metadata join table
	 *
	 * @param array $criteria_list
	 * @throws Exception
	 * @return DatabaseQueryTable
	 */
	protected function _find(array $criteria_list)
	{
		$table = $this->_getTable();

		foreach($criteria_list as $criteria) {
			if($this->_isMetadata($criteria['name'])) {
				$table = $this->_finderAddMetadataWhere($table, $criteria);
			} else {
				$table = $this->_finderAddMainWhere($table, $criteria);
			}
		}

		return $table;
	}

	/**
	 * Add a where to the main table
	 *
	 * @param DatabaseQueryTable 	$table
	 * @param array					$criteria
	 * @throws Exception
	 * @return DatabaseQueryTable
	 */
	private function _finderAddMainWhere($table, $criteria)
	{
		$table->addWhere($criteria['name'], $criteria['value'], $criteria['context'], $criteria['operator']);
		return $table;
	}

	/**
	 * Add a where to the metadata table join which references the member
	 *
	 * @param DatabaseQueryTable 	$table
	 * @param array 				$criteria
	 * @throws Exception
	 * @return DatabaseQueryTable
	 */
	private function _finderAddMetadataWhere($table, $criteria)
	{
		$meta_name = $this->_getMetadataTableName();
		$join_list = $table->getJoinList();

		$meta_table = null;
		foreach($join_list as $join) {
			if($meta_name != $join->getJoinTable()->getName()) {
				continue;
			}

			$column_list = $join->getJoinTable()->getColumnList();
			foreach($column_list as $column) {
				if($criteria['name'] != $column->getAlias()) {
					continue;
				}

				$meta_table = $join->getJoinTable();
			}
		}

		if(is_null($meta_table)) {
			throw new Exception('No metadata table found in main table');
		}

		$meta_table->addWhere('value', $criteria['value']);
		return $table;
	}

	/**
	 * Create a finder criteria array from the passed arguments
	 *
	 * @param string 	$name
	 * @param mixed 	$value
	 * @param integer 	$context
	 * @param integer 	$operator
	 * @return array
	 */
	final public function getFinderCriteria($name, $value, $context = null, $operator = null)
	{
		return array(
			'name' 		=> $name,
			'value' 	=> $value,
			'context' 	=> $context,
			'operator' 	=> $operator
		);
	}

	/**
	 * Attempt to load a record based on the current primary key value.
	 *
	 * @param mixed $id
	 * @throws Exception
	 */
	final public function load($id)
	{
		$primary = $this->getPrimaryKey();
		if(empty($primary)) {
			throw new Exception('A SuperModel must have a primary key to use the load method');
		}

		$this->setPrimaryKeyValue($id);
		$criteria_list = array();

		foreach($primary as $key) {
			$criteria_list[] = self::getFinderCriteria($key, $this->$key);
		}

		$result = $this->find($criteria_list, self::FINDER_SINGLE);
		if(!$result) {
			$message = 'Model record not found';

			if($error = $this->_db->getErrorMsg()) {
				$message .= sprintf(': %s', $error);
			}

			return;
		}

		return $result;
	}

	/**
	 * Insert or update the current record based on the existence of a primary key value. Will call internal
	 * validation so be ready with your try {} catch {} blocks kids.
	 *
	 * @param boolean $force_insert
	 * @return mixed
	 */
	final public function save($force_insert = false, $validate_before_save = true)
	{
		if ($validate_before_save) {
			$this->_validate();
		}
		
		if($this->_isPrimarySet() && !$force_insert) {
			$result = $this->_update();
		} else {
			$result = $this->_insert();
			
			if($this->_isSimulation()){
				return $result;
			}

			if($result !== false) {
				$this->setPrimaryKeyValue($result);
			}
		}

		if($result !== false) {
			$metadata_list = $this->getMetadataMemberList();
			if(!empty($metadata_list)) {
				$key_value = $this->getPrimaryKeyValue();
				if(is_array($key_value)) {
					throw new Exception('Metadata can not be used for tables with a composite PK');
				}

				$this->_saveMetadataList($key_value, $metadata_list);
			}
		}

		return $result;
	}
	
	final public function getSQL(){
		$this->_simulation_mode = true;
		
		return $this->save(false,false);
	}
	
	/**
	 * Delete a record from a table by primary key.
	 *
	 * @return bool
	 */
	final public function delete()
	{
		$table = new DatabaseQueryTable($this->_table_name);

		$primary = $this->getPrimaryKey();
		foreach($primary as $key) {
			$table->addWhere($key, $this->$key);
		}

		$query 	= new DatabaseQuery($table);
		$db 	=& JFactory::getDBO();

		$db->setQuery($query->getDelete());
		
		$table->__destruct();
		unset($table);
		
		return $db->query();
	}

	/**
	 * Call internal validation methods.
	 */
	protected function _validate()
	{
		$this->_primaryKeyCheck();

		$this->_transformMembers();

		$this->_emptyCheck();
		$this->_typeCheck();

		$this->_valueCheck();

		$error_list = $this->getErrorList();
		if(!empty($error_list)) {
			throw new Exception('Save failed due to validation errors');
		}
	}

	/**
	 * Public validation trigger to assist control-flow. This will catch any validation
	 * exceptions and return the internal error stack if required, so it's useful for
	 * creating a submit/validate flow for admin.
	 *
	 * @return mixed
	 */
	public function validate()
	{
		try {
			$this->_validate();
		} catch(Exception $e) {
			return $this->getErrorList();
		}

		return array();
	}

	/**
	 * Check that the model doesn't use a composite primary key and metadata together
	 */
	protected function _primaryKeyCheck()
	{
		$metadata_list = $this->getMetadataMemberList();

		if(!empty($metadata_list)) {
			$key = $this->getPrimaryKey();

			if(is_array($key) && count($key) > 1) {
				$this->_addError('Can\'t use metadata with a composite foreign key');
			}
		}
	}

	/**
	 * Check that all required members have values.
	 */
	protected function _emptyCheck()
	{
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('required', $parameter_list) && $parameter_list['required'] === true) {
				if($this->$member === self::UNDEFINED || is_null($this->$member) || $this->$member === '') {
					$this->_addError("{$parameter_list['name']} is undefined", $member);
				}
			}
		}
	}

	/**
	 * Check that all internal members are of the correct type.
	 */
	protected function _typeCheck()
	{
		foreach($this->_member_list as $member => $parameter_list) {
			if((array_key_exists('primary', $parameter_list) && $parameter_list['primary']) || $this->$member == self::UNDEFINED) {
				continue;
			}

			switch($parameter_list['type']) {
				case self::TYPE_INTEGER:
					$result = is_int($this->$member);
					break;
				case self::TYPE_FLOAT:
					$result = is_float($this->$member);
					break;
				case self::TYPE_STRING:
					$result = is_string($this->$member);
					break;
				default:
					$result = true;
					break;
			}

			if(!$result) {
				$this->_addError("{$parameter_list['name']} is not the correct type", $member);
			}
		}
	}

	/**
	 * Check that internal members have valid values.
	 */
	protected function _valueCheck()
	{
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('validate', $parameter_list) && method_exists($this, $parameter_list['validate'])) {
				$method = $parameter_list['validate'];
				$this->$method($this->$member);
			}
		}
	}

	/**
	 * Perform transformations on members which need to be modified before saving.
	 */
	protected function _transformMembers()
	{
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('transform', $parameter_list) && method_exists($this, $parameter_list['transform'])) {
				$method = $parameter_list['transform'];
				$this->$method();
			}
		}
	}

	/**
	 * Add an error to the internal stack.
	 *
	 * @param string $error
	 * @param string $member
	 */
	final protected function _addError($error, $member = null)
	{
		if(is_null($member)) {
			$member = self::ERROR_MEMBER_MODEL;
		}

		if(!isset($this->_error_list[$member])) {
			$this->_error_list[$member] = array();
		}

		$this->_error_list[$member][] = $error;
	}
	
	/**
	 * Unset Error list
	 *
	 * @param string $member
	 */
	final public function clearErrorList($member = null)
	{
		if (!is_null($member)) {
			unset($this->_error_list[$member]);
		} else {
			$this->_error_list = array();	
		}
	}

	/**
	 * Get the internal error stack
	 *
	 * @return array
	 */
	final public function getErrorList()
	{
		return $this->_error_list;
	}

	/**
	 * Insert a new record.
	 *
	 * @throws Exception
	 */
	final protected function _insert()
	{
		$table = new DatabaseQueryTable($this->_table_name);

		$metadata_list = array();
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('metadata', $parameter_list) && $parameter_list['metadata']) {
				continue;
			}

			if($parameter_list['type'] === self::TYPE_DATETIME_UPDATED) {
				continue;
			}

			if($parameter_list['type'] === self::TYPE_DATETIME_CREATED) {
				$value = new DatabaseQueryTableValueFunction(DatabaseQueryHelperFunction::NOW);
			} else {
				if($this->$member === self::UNDEFINED) {
					continue;
				}

				$value = new DatabaseQueryTableValue($this->$member);
			}

			$table->addColumn(new DatabaseQueryTableColumn($member, null, $value));
		}

		$query 	= new DatabaseQuery($table);
		$db 	=& JFactory::getDBO();

		$db->setQuery($query->getInsert());
		
		if($this->_isSimulation()){
			return $this->_sql;	
		}
		
		if(!$db->query()) {
			throw new Exception('Insert query failed');
		}
		
		$table->__destruct();
		unset($table);

		return $db->insertId();
	}

	/**
	 * Update an existing record.
	 *
	 * @throws Exception
	 */
	final protected function _update()
	{
		$table = new DatabaseQueryTable($this->_table_name);
		
		foreach($this->_member_list as $member => $parameter_list) {
			if(array_key_exists('metadata', $parameter_list) && $parameter_list['metadata']) {
				continue;
			}

			if(array_key_exists('primary', $parameter_list) && $parameter_list['primary']) {
				continue;
			}

			if($parameter_list['type'] === self::TYPE_DATETIME_CREATED) {
				continue;
			}

			if($parameter_list['type'] === self::TYPE_DATETIME_UPDATED) {
				$value = new DatabaseQueryTableValueFunction(DatabaseQueryHelperFunction::NOW);
			} else {
				$member_value = $this->$member;
				
				if ($member_value == self::UNDEFINED && array_key_exists($member, $this->_data)) {
					$member_value = $this->_data[$member];
				}
				
				if($member_value === self::UNDEFINED) {
					continue;
				}

				$value = new DatabaseQueryTableValue($member_value);
			}

			$table->addColumn(new DatabaseQueryTableColumn($member, null, $value));
		}

		$primary = $this->getPrimaryKey();
		foreach($primary as $key) {
			$table->addWhere($key, $this->$key);
		}

		$query 	= new DatabaseQuery($table);
		$db		=& JFactory::getDBO();

		$db->setQuery($query->getUpdate());
		
		if($this->_isSimulation()){		
			return $this->_sql;
		}
		
		if(!$db->query()) {
			throw new Exception('Update query failed');
		}

		$table->__destruct();
		unset($table);
		
		return true;
	}

	/**
	 * Process a list of metadata fields
	 *
	 * @param mixed $key_value
	 * @param array $metadata_list
	 */
	private function _saveMetadataList($key_value, $metadata_list)
	{
		foreach($metadata_list as $member) {
			if($this->$member == self::UNDEFINED) {
				$this->$member = null;
			}

			$this->_saveMetadata($key_value, $member, $this->$member);
		}
	}

	/**
	 * Save a single metadata record
	 *
	 * @param mixed 	$key_value
	 * @param string 	$name
	 * @param mixed 	$value
	 * @return bool
	 */
	private function _saveMetadata($key_value, $name, $value)
	{
		$table_name 	= $this->_getMetadataTableName();
		$foreign_key 	= $this->_getMetadataForeignKeyName();

		$table = new DatabaseQueryTable($table_name);

		$table	->addColumn(new DatabaseQueryTableColumn($foreign_key, null, new DatabaseQueryTableValue($key_value)))
				->addColumn(new DatabaseQueryTableColumn('name', null, new DatabaseQueryTableValue($name)))
				->addColumn(new DatabaseQueryTableColumn('value', null, new DatabaseQueryTableValue($value)));

		$query = new DatabaseQuery($table);
		$db =& JFactory::getDBO();

		$insert = $query->getSmartInsert();
		$db->setQuery($insert);
		
		$table->__destruct();
		unset($table);

		return $db->query();
	}

	/**
	 * Store method to use when updating models to use SuperModel
	 *
	 * @param array $parameter_list
	 * @return mixed
	 */
	final public function store($parameter_list)
	{
		$class = get_class($this);
		$model = new $class($parameter_list);

		return $model->save();
	}

	/**
	 * Get the friendly name for a member
	 *
	 * @param string $member
	 * @return string
	 */
	final public function getDisplayName($member)
	{
		return (array_key_exists($member, $this->_member_list)) ? $this->_member_list[$member]['name'] : $member;
	}
}