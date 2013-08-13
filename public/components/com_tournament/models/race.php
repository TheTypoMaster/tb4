<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
require_once 'event.php';

class TournamentModelRace extends TournamentModelEvent
{
	/**
	 * Betting open
	 *
	 * @var string
	 */
	const STATUS_SELLING = 'selling';

	/**
	 * Betting closed
	 *
	 * @var string
	 */
	const STATUS_CLOSED = 'closed';

	/**
	 * Interim results available
	 *
	 * @var string
	 */
	const STATUS_INTERIM = 'interim';

	/**
	 * Photo finish
	 *
	 * @var string
	 */
	const STATUS_PHOTO = 'photo';

	/**
	 * Contested result
	 *
	 * @var string
	 */
	const STATUS_PROTEST = 'protest';

	/**
	 * Paying bets
	 *
	 * @var string
	 */
	const STATUS_PAYING	= 'paying';

	/**
	 * Abandoned
	 *
	 * @var string
	 */
	const STATUS_ABANDONED = 'abandoned';

	/**
	 * Statuses which indicate that results are available
	 *
	 * @var array
	 */
	private static $result_status_list = array(
		self::STATUS_INTERIM,
		self::STATUS_PAYING
	);

	/**
	 * Metadata member list
	 *
	 * @var array
	 */
	protected $_member_list = array(
		'distance' => array(
			'name' 		=> 'Distance',
			'type' 		=> self::TYPE_STRING
		),
		'class' => array(
			'name' 		=> 'Class',
			'type' 		=> self::TYPE_STRING
		),
		'number' => array(
			'name' 		=> 'Number',
			'type' 		=> self::TYPE_INTEGER
		),
		'trifecta_pool' => array(
			'name' 		=> 'Trifecta Pool',
			'type' 		=> self::TYPE_FLOAT
		),
		'firstfour_pool' => array(
			'name' 		=> 'FirstFour Pool',
			'type' 		=> self::TYPE_FLOAT
		),
		'exacta_pool' => array(
			'name' 		=> 'Exacta Pool',
			'type' 		=> self::TYPE_FLOAT
			),
		'quinella_pool' => array(
			'name' 		=> 'Quinella Pool',
			'type' 		=> self::TYPE_FLOAT
		),
		'trifecta_dividend' => array(
			'name' 		=> 'Trifecta Dividend',
			'type' 		=> self::TYPE_STRING
		),
		'firstfour_dividend' => array(
			'name' 		=> 'FirstFour Dividend',
			'type' 		=> self::TYPE_STRING
		),
		'exacta_dividend' => array(
			'name' 		=> 'Exacta Dividend',
			'type' 		=> self::TYPE_STRING
		),
		'quinella_dividend' => array(
			'name' 		=> 'Quinella Dividend',
			'type' 		=> self::TYPE_STRING
		),
		'external_race_pool_id_list' => array(
			'name' 		=> 'External Race Pool Id List',
			'type' 		=> self::TYPE_STRING
		),
		'event_id' => array(
			'name' 		=> 'BM Event ID',
			'type' 		=> self::TYPE_INTEGER
		)

	);

	/**
	 * Load a single record from the #__race table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getRace($id)
	{
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), self::FINDER_SINGLE);
	}
	
	/**
	 * Load a single record from the #__race table by ID for Api.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getRaceApi($id)
	{
		$db =& $this->getDBO();
		$query = "SELECT e.id, e.tournament_competition_id, e.external_event_id, e.wagering_api_id, e.event_status_id, e.paid_flag, e.name, e.start_date, e.created_date, e.updated_date, e.distance, e.class, e.number, e.trifecta_pool, e.firstfour_pool, e.exacta_pool, e.quinella_pool, e.trifecta_dividend, e.firstfour_dividend, e.exacta_dividend, e.quinella_dividend, e.external_race_pool_id_list FROM `tbdb_event` AS e WHERE e.id =".$id;
		//$query = "SELECT * FROM `#__race` WHERE `id`=".$id;
		$db->setQuery($query);

		return $db->loadObject();
	}
	
	/**
	 * Determine the race id from event_id and race number
	 *
	 * @param integer $event_id
	 * @param integer $race_num 
	 * @return object
	 */
	public function getRaceIdByEventIdRaceNum($event_id, $race_num)
	{
		$db =& $this->getDBO();
		
		$query = 'SELECT e.id FROM '.$db->nameQuote('#__event').' AS e 
		INNER JOIN '.$db->nameQuote('#__event_group_event').' AS ege ON e.id = ege.event_id 
		WHERE e.NUMBER = '.$db->quote($race_num).' 
		AND ege.event_group_id = '.$db->quote($event_id).' 
		GROUP BY e.id';

		$db->setQuery($query);

		return $db->loadObject();
	}
	
	/**
	 * Get the today's next races
	 *
	 * @param integer $event_type_id
	 * @param integer $limit
	 * @return array
	 */
	public function getTodayNextRaceListByMeetingTypeID($meeting_type_id =null, $limit = null)
	{
		$sport_table	= new DatabaseQueryTable('#__tournament_sport');
		$sport_table	->addWhere('racing_flag', 1);
		
		$competition_table	= new DatabaseQueryTable('#__tournament_competition');
		$competition_table	->addColumn(new DatabaseQueryTableColumn('name', 'competition_name'))
							->addJoin($sport_table, 'tournament_sport_id', 'id');
		
		
		$event_status_table	= new DatabaseQueryTable('#__event_status');
		$event_status_table	->addWhere('keyword', self::STATUS_SELLING);
		
		$event_group_table	= new DatabaseQueryTable('#__event_group');
		$event_group_table	->addJoin($competition_table, 'tournament_competition_id', 'id')
							->addColumn(new DatabaseQueryTableColumn('name', 'meeting_name'))
							->addColumn(new DatabaseQueryTableColumn('id', 'meeting_id'));
							//->addFunctionWhere('start_date', 'NOW()', DatabaseQueryTableWhere::CONTEXT_AND, DatabaseQueryTableWhere::OPERATOR_GREATER_THAN)
							//->addFunctionWhere('start_date', 'DATE_ADD(CONCAT(CURDATE(), " 00:00:00"), INTERVAL 1 DAY)', DatabaseQueryTableWhere::CONTEXT_AND, DatabaseQueryTableWhere::OPERATOR_LESS_THAN);
							
		if (!empty($meeting_type_id)) {
			$event_group_table->addWhere('tournament_competition_id', $meeting_type_id);
		}
							
		$event_group_event_table	= new DatabaseQueryTable('#__event_group_event');
		$event_group_event_table	->addJoin($event_group_table, 'event_group_id', 'id', null, false);
		
		$table	= $this->_getTable()
				->addJoin($event_group_event_table, 'id', 'event_id')
				->addJoin($event_status_table, 'event_status_id', 'id')
				->addFunctionWhere('start_date', DatabaseQueryHelperFunction::NOW,
					DatabaseQueryTableWhere::CONTEXT_AND,
					DatabaseQueryTableWhere::OPERATOR_GREATER_THAN)
				->addOrder('start_date', DatabaseQueryTableOrder::ASCENDING);		
				
		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect(), 0, $limit);
		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Helper method used to generate queries for race numbers
	 *
	 * @param integer $id
	 * @param integer $operator
	 * @return integer
	 */
	private function _getRaceNumberByTournamentID($id, $operator = null)
	{
		if(is_null($operator)) {
			$operator = DatabaseQueryTableWhere::OPERATOR_GREATER_THAN;
		}

		$tem_table = new DatabaseQueryTable('#__event_group_event');

		$tem_table	->addJoin(new DatabaseQueryTable('#__tournament_event_group'), 'event_group_id', 'event_group_id', null, false)->getTable()
					->addJoin(new DatabaseQueryTable('#__tournament'), 'tournament_id', 'id');

		$match_table = new DatabaseQueryTable($this->_table_name);

		$match_table->addColumn('external_event_id')
					->addWhere('start_date', 'NOW()', DatabaseQueryTableWhere::CONTEXT_AND, $operator)

					->addOrder('start_date')
					->addJoin($tem_table, 'id', 'event_id');

		$db =& $this->getDBO();
		$query = new DatabaseQuery($match_table, 1);

		$db->setQuery($query->getSelect());
		
		return $db->loadResult();
	}

	/**
	 * Get the next race number for a tournament
	 *
	 * @param integer $id
	 * @return integer
	 */
	public function getNextRaceNumberByTournamentID($id)
	{
		//return $this->_getRaceNumberByTournamentID($id);
		
		$db =& $this->getDBO();
		$query =
			'SELECT
				e.number
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.event_group_id = eg.id
			WHERE
				t.id = ' . $db->quote($id) . '
			AND
				e.start_date > NOW()
			ORDER BY
				e.start_date ASC
			LIMIT 1';

		$db->setQuery($query);
		return $db->loadResult();
		
	}

	/**
	 * Get the number of the last race to jump in a tournament
	 *
	 * @param integer $id
	 * @return integer
	 */
	public function getLastToJumpNumberByTournamentID($id)
	{
		//return $this->_getRaceNumberByTournamentID($id, DatabaseQueryTableWhere::OPERATOR_LESS_THAN);
		$db =& $this->getDBO();
		$query =
			'SELECT
				e.number
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.event_group_id = eg.id
			WHERE
				em.name = "number"
			AND
				t.id = ' . $db->quote($id) . '
			AND
				e.start_date < NOW()
			ORDER BY
				e.start_date DESC
			LIMIT 1';

		$db->setQuery($query);
		return $db->loadResult();
		
		
	}

	/**
	 * Get a list of races by tournament ID
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRaceListByTournamentID($id)
	{
//		$table = $this->_getTable()
//			->addJoin(new DatabaseQueryTable('#__event_group_event'), 'id', 'event_id', null, false)
//				->getTable()->addJoin(new DatabaseQueryTable('#__tournament_event_group'), 'event_group_id', 'event_group_id', null, false)
//					->getTable()->addJoin(new DatabaseQueryTable('#__tournament'), 'tournament_id', 'id', null, false)
//						->getTable()->addWhere('id', $id);
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
//
//		return $this->_loadModelList($db->loadObjectList());

		$db =& $this->getDBO();
		$query =
			'SELECT
				e.id,
				ege.event_group_id AS meeting_id,
				e.name,
				e.start_date,
				e.paid_flag,
				e.event_status_id,
				e.class,
				e.distance,
				e.number,
				e.created_date,
				e.updated_date,
				t.id AS tournament_id
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.group_event_id = ege.group_event_id
			WHERE
				t.id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObjectList();
		
		//t.tournament_id = ' . $db->quote($id)
		
	}
	
	/**
	 * Get a list of races by tournament ID for API
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRaceListByTournamentIDApi($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				e.id,
				ege.event_group_id AS meeting_id,
				e.name,
				e.start_date,
				e.paid_flag,
				e.event_status_id,
				e.class,
				e.distance,
				e.number,
				e.created_date,
				e.updated_date,
				t.id AS tournament_id
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.event_group_id = ege.event_group_id
			WHERE
				t.id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObjectList();
		
	}

	/**
	 * Get the start times of all the races in a meeting
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRaceTimesByMeetingID($id)
	{
		/*$table = $this->_getTable();

		$join = new DatabaseQueryTable('#__event_group_event');
		$join->addWhere('event_group_id', $id);

		$table	->addColumn('external_event_id')
				->addColumnFunction('start_date', 'time', null, DatabaseQueryTableFunction::UNIX_TIMESTAMP)
				->addOrder('external_event_id')
				->addJoin($join, 'id', 'event_id');*/


		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
		
		//XXX: use super model
		$query = '
		SELECT
			e.id,
			e.tournament_competition_id,
			e.external_event_id,
			e.wagering_api_id,
			e.event_status_id,
			e.paid_flag,
			e.name,
			e.start_date,
			e.created_date,
			e.updated_date,
			UNIX_TIMESTAMP(e.start_date) AS time,
			e.distance,
			e.class,
			e.number
		FROM
			'. $db->nameQuote('#__event') . ' AS e
		INNER JOIN
			'. $db->nameQuote('#__event_group_event') . ' AS ege
		ON
			e.id = ege.event_id
		WHERE
			ege.event_group_id = ' . $db->quote($id) . '
		GROUP BY e.id
		ORDER BY e.number
		';
		$db->setQuery($query);
		return $this->_loadModelList($db->loadObjectList('number'));
	}

	/**
	 * Get the start times of all the races in a meeting (API)
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRaceTimesByMeetingIDApi($id)
	{
		$db =& $this->getDBO();

		$query = '
		SELECT
			e.id,
			e.tournament_competition_id,
			e.external_event_id,
			e.wagering_api_id,
			e.event_status_id,
			e.paid_flag,
			e.name,
			e.start_date,
			e.created_date,
			e.updated_date,
			UNIX_TIMESTAMP(e.start_date) AS time,
			e.distance,
			e.class,
			e.number
		FROM
			'. $db->nameQuote('#__event') . ' AS e
		INNER JOIN
			'. $db->nameQuote('#__event_group_event') . ' AS ege
		ON
			e.id = ege.event_id
		WHERE
			ege.event_group_id = ' . $db->quote($id) . '
		GROUP BY e.id
		ORDER BY e.number
		';
		$db->setQuery($query);
		return $db->loadObjectList('number');
	}

	/**
	 * Get the list of races for a meeting
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRaceListByMeetingID($id)
	{
		$join = new DatabaseQueryTable('#__event_group_event');
		$join->addWhere('event_group_id', $id);

		$table 	= $this->_getTable()->addJoin($join, 'id', 'event_id');
		$table->addOrder('number', DatabaseQueryTableOrder::ASCENDING);

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Get a specific race in a meeting
	 *
	 * @param integer $meeting_id
	 * @param integer $number
	 * @return object
	 */
	public function getRaceByMeetingIDAndNumber($meeting_id, $number)
	{
//		$event_group_event_table	= new DatabaseQueryTable('#__event_group_event');
//		$event_status_table			= new DatabaseQueryTable('#__event_status');
//		
//		$event_status_table->addColumn('keyword', 'event_status');
//		
//		$table = $this->_getTable()
//			->addWhere('number', $number)
//			->addJoin($event_group_event_table, 'id', 'event_id')
//			->addJoin($event_status_table, 'event_status_id', 'id');
//			
//		$event_group_event_table->addWhere('event_group_id', $meeting_id);
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
//
//		$meeting_id	= $db->quote($meeting_id);
//		$number		= $db->quote($number);
		
		$db =& $this->getDBO();
		//XXX: use super model
		$query = '
		SELECT
			e.id,
			e.event_id,
			e.tournament_competition_id,
			e.external_event_id,
			e.wagering_api_id,
			e.event_status_id,
			e.paid_flag,
			e.name,
			e.start_date,
			e.created_date,
			e.updated_date,
			e.distance,
			e.class,
			e.number,
			e.trifecta_dividend,
			e.firstfour_dividend,
			e.exacta_dividend,
			e.quinella_dividend,
			e.distance,
			e.weather,
			e.track_condition,
			es.keyword
		FROM
			' . $db->nameQuote('#__event') . ' AS e
		INNER JOIN
			'. $db->nameQuote('#__event_group_event') . ' AS ege
		ON
			e.id = ege.event_id
		INNER JOIN
			' . $db->nameQuote('#__event_status') . ' AS es
		ON
			e.event_status_id = es.id
		WHERE
			e.number = ' . $db->quote($number) . '
		AND
			ege.event_group_id = '. $db->quote($meeting_id) . '
		GROUP BY e.id ORDER BY e.updated_date DESC
		';
		$db->setQuery($query);
		return $this->_loadModel($db->loadObject());
	}

    /**
	 * Get a specific race in a meeting (API)
	 *
	 * @param integer $meeting_id
	 * @param integer $number
	 * @return object
	 */
	public function getRaceByMeetingIDAndNumberApi($meeting_id, $number)
	{
		$db =& $this->getDBO();
		$query = '
		SELECT
			e.id,
			e.tournament_competition_id,
			e.external_event_id,
			e.wagering_api_id,
			e.event_status_id,
			e.paid_flag,
			e.name,
			e.start_date,
			e.created_date,
			e.updated_date,
			e.distance,
			e.class,
			e.number,
			e.trifecta_dividend,
			e.firstfour_dividend,
			e.exacta_dividend,
			e.quinella_dividend,
			es.keyword
		FROM
			' . $db->nameQuote('#__event') . ' AS e
		INNER JOIN
			'. $db->nameQuote('#__event_group_event') . ' AS ege
		ON
			e.id = ege.event_id
		INNER JOIN
			' . $db->nameQuote('#__event_status') . ' AS es
		ON
			e.event_status_id = es.id
		WHERE
			e.number = ' . $db->quote($number) . '
		AND
			ege.event_group_id = '. $db->quote($meeting_id) . '
		GROUP BY e.id
		';
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Helper used to load helpful information when processing races
	 *
	 * @param array $status
	 * @return array
	 */
	private function _getUnpaidRaceListByStatus($status)
	{
		if(!is_array($status)) {
			$status = array($status);
		}

		$db =& $this->getDBO();

		$join = new DatabaseQueryTable('#__event_status');
		$join->addInWhere('keyword', $status);

		$table = $this->_getTable()
			->addWhere('paid_flag', '1', null, DatabaseQueryTableWhere::OPERATOR_GREATER_OR_LESS_THAN)
			->addFunctionWhere('start_date', DatabaseQueryHelperFunction::NOW, null, DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL)
			->addJoin($join, 'event_status_id', 'id');

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Get recent races which need to be checked for status updates
	 *
	 * @return array
	 */
	public function getRecentRaceList()
	{
		return $this->_getUnpaidRaceListByStatus(array(
			self::STATUS_SELLING,
			self::STATUS_PHOTO,
			self::STATUS_PROTEST,
			self::STATUS_INTERIM,
			self::STATUS_PAYING,
			self::STATUS_CLOSED
		));
	}

	/**
	 * Get paying races which need to be checked for results
	 *
	 * @return array
	 */
	public function getPayingRaceList()
	{
		return $this->_getUnpaidRaceListByStatus(self::STATUS_PAYING);
	}

	/**
	 * Get abandoned races which need to be refunded
	 *
	 * @return array
	 */
	public function getAbandonedRaceList()
	{
		return $this->_getUnpaidRaceListByStatus(self::STATUS_ABANDONED);
	}

	/**
	 * Update a race record to set the paid_flag
	 *
	 * @param integer $id
	 * @return bool
	 */
	public function setRaceToPaid($id)
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table	->addColumn(	'paid_flag', 1)
				->addFunction(	'updated_date', 'NOW()')
				->addWhere(		'id', $id);

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $db->query();
	}

	/**
	 * Check if a string status indicates that results are available
	 *
	 * @param string $status
	 * @return bool
	 */
	public function isResultAvailable($status)
	{
		return (in_array($status, self::$result_status_list));
	}

	/**
	 * Get the first race number of a meeting
	 *
	 * @param integer $meeting_id
	 * @return integer
	 */
	public function getFirstRaceNumberByMeetingID($meeting_id)
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table	->addColumn('external_event_id')
				->addWhere('event_id', $meeting_id)
				->addOrder('start_date', DatabaseQueryTableOrder::ASCENDING);

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table, 1);
		$db->setQuery($query->getSelect());

		return $db->loadResult();
	}

	/**
	 * Get the last race number of a meeting
	 *
	 * @param integer $meeting_id
	 * @return integer
	 */
	public function getLastRaceNumberByMeetingID($meeting_id)
	{
//		$table = new DatabaseQueryTable($this->_table_name);
//		$table	->addColumn('number')
//				->addWhere('event_id', $meeting_id)
//				->addOrder('start_date', DatabaseQueryTableOrder::DESCENDING);
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
//
//		return $db->loadResult();
		
		$db =& $this->getDBO();
		$query = '
			SELECT
				e.number
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			LEFT JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				ege.event_id = e.id
			WHERE
				ege.event_group_id = ' . $db->quote($meeting_id) . '
			ORDER BY
				e.start_date DESC
			LIMIT 1
		';
		
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get a count of races in a given meeting which have a particular status
	 *
	 * @param integer $meeting_id
	 * @param integer $status
	 * @return integer
	 */
	public function getRaceCountByMeetingIDAndStatus($meeting_id, $status)
	{
		$join = new DatabaseQueryTable('#__event_group_event');
		$join	->addWhere('event_group_id', $meeting_id);

		$table = new DatabaseQueryTable($this->_table_name);
		$table	->addFunction('*', DatabaseQueryTableFunction::COUNT)
				->addWhere('event_status_id', $status)
				->addJoin($join, 'id', 'event_id');

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $db->loadResult();
	}
	
	/**
	 * Get the next race number for a meeting
	 *
	 * @param integer $id
	 * @return integer
	 */
	public function getNextRaceNumberByMeetingID($id) {
		//return $this->_getRaceNumberByMeetingID($id);
		$db =& $this->getDBO();
		$query =
			'SELECT
				e.number
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.event_group_id = eg.id
			WHERE
				ege.event_group_id = ' . $db->quote($id) . '
			AND
				e.start_date > NOW()
			ORDER BY
				e.start_date ASC
			LIMIT 1';

		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Helper method used to generate queries for race numbers
	 *
	 * @param integer $id
	 * @param integer $operator
	 * @return integer
	 */
	private function _getRaceNumberByMeetingID($id, $operator = null) {
		if(is_null($operator)) {
			$operator = DatabaseQueryTableWhere::OPERATOR_GREATER_THAN;
		}

		$tem_table = new DatabaseQueryTable('#__event_group_event');

		$tem_table	->addWhere('event_group_id', $id)
					->addJoin(new DatabaseQueryTable('#__event_group'), 'event_group_id', 'id', null, false);


		$match_table = new DatabaseQueryTable($this->_table_name);

		$match_table->addColumn('external_event_id')
					->addFunctionWhere('start_date', 'NOW()', DatabaseQueryTableWhere::CONTEXT_AND, $operator)
					->addOrder('start_date')
					->addJoin($tem_table, 'id', 'event_id');

		$db =& $this->getDBO();
		$query = new DatabaseQuery($match_table, 1);

		$db->setQuery($query->getSelect());
		
		return $db->loadResult();
	}
}
