<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');

class TournamentModelTournamentSport extends SuperModel
{
	/**
	 * Array to content the Racing Sports to exclude from the list
	 * @var Array
	 */
	public $excludeSports = array(
		'galloping',
		'greyhounds',
		'harness'
	);
	
	protected $_table_name = '#__tournament_sport';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'name' => array(
			'name' 		=> 'Name',
			'type' 		=> self::TYPE_STRING,
			'required' 	=> true
		),
		'description' => array(
			'name' 		=> 'Description',
			'type' 		=> self::TYPE_STRING
		),
		'status_flag' => array(
			'name' => 'Status-flag',
			'type' => self::TYPE_INTEGER
		),
		'racing_flag' => array(
			'name' => 'Racing flag',
			'type' => self::TYPE_INTEGER
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		)
	);

	/**
	 * Load a single sport record by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentSport($id)
	{
		//return $this->load($id);
		$db =& $this->getDBO();
		$query =
			'SELECT
				s.id,
				s.name,
				s.description,
				s.status_flag,
				s.racing_flag,
				s.created_date,
				s.updated_date,
				sm.external_sport_id
			FROM
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			LEFT JOIN
				' . $db->nameQuote('#__sport_map') . ' AS sm
			ON
				s.id = sm.tournament_sport_id
			WHERE
				s.id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Load a sport by name
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getTournamentSportByName($name)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('name', $name)
		), 	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Load a list of sports by status. Defaults to active ones only.
	 *
	 * @param integer $status
	 * @return object
	 */
	public function getTournamentSportListByStatus($status_id = 1)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('status_flag', $status_id)
		), 	SuperModel::FINDER_LIST, 'id');
	}

	/**
	* Load all the sports from the Table - used to generate the sports drop down.
	*
	* @return array
	*/
	public function getTournamentSportList()
	{
		return $this->getTournamentSportListByStatus(1);
	}

	/**
	 * Get a full list of sports
	 *
	 * @return TournamentModelTournamentSport
	 */
	public function getTournamentSportAdminList($type = null)
	{
		$db =& $this->getDBO();

		$exclude_list = array();
		foreach($this->excludeSports as $sport) {
			$exclude_list[] = $db->quote(strtolower($sport));
		}

		$query = '
			SELECT
				id,
				name
			FROM
				' . $db->nameQuote('#__tournament_sport');
		
		switch ($type) {
			case 'sports':
				$query .= '
					WHERE
						LOWER(name) NOT IN (' . implode(',', $exclude_list) . ')
				';
				break;
			case 'racing':
				$query .= '
					WHERE
						LOWER(name) IN (' . implode(',', $exclude_list) . ')
				';
				break;
			
		}
		
		$query .='
			ORDER BY
				name ASC
		';

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get the list of sport names keyed with their external IDs
	 *
	 * @return array
	 */
	public function getTournamentSportExternalList()
	{
		$map = new DatabaseQueryTable('#__sport_map');
		$map->addColumn('external_sport_id');

		$table = new DatabaseQueryTable($this->_table_name);
		$table	->addColumn('name')
				->addJoin($map, 'id', 'tournament_sport_id');

		$db 	=& $this->getDBO();
		$query 	= new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		$object_list = $db->loadObjectList();

		$option_list = array();
		foreach($object_list as $object) {
			$option_list[$object->external_sport_id] = $object->name;
		}

		return $option_list;
	}

	/**
	 * Get the total sport count
	 *
	 * @return integer
	 */
	public function getTournamentSportCount()
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table->addColumn(new DatabaseQueryTableColumnFunction('*'));

		$db 	=& $this->getDBO();
		$query 	= new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $db->loadResult();

	}
	
	/**
	 * Check out if sport is racing - look up by tournament id
	 *
	 * @param int $tournament
	 * @return boolean
	 */
	public function isRacingByTournamentId($tournament_id){
		$db =& $this->getDBO();
		$query =
			'SELECT
				ts.name
			FROM
				' . $db->nameQuote('#__tournament_sport') . ' AS ts
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				ts.id = t.tournament_sport_id
			WHERE
				t.id = '.$db->quote($tournament_id);

		$db->setQuery($query);
		$sport_name = $db->loadResult();

		return in_array($sport_name, $this->excludeSports);
	}
}