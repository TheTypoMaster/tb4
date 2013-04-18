<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
require_once 'eventgroup.php';

class TournamentModelMeeting extends TournamentModelEventGroup
{
	protected $_member_list = array(
		'meeting_code' => array(
			'name' 		=> 'Meeting Code',
			'type' 		=> self::TYPE_STRING
		),
		'state' => array(
			'name' 		=> 'State',
			'type' 		=> self::TYPE_STRING
		),
		'events' => array(
			'name' 		=> 'Number of Races',
			'type' 		=> self::TYPE_INTEGER
		),
		'track' => array(
			'name' 		=> 'Track Conditions',
			'type' 		=> self::TYPE_STRING
		),
		'weather' => array(
			'name' 		=> 'Weather Conditions',
			'type' 		=> self::TYPE_STRING
		),
		'type_code' => array(
			'name'		=> 'Meeting Type Code',
			'type' 		=> self::TYPE_STRING
		),
	);

	private $type_list = array(
		'r' => 'galloping',
		'g' => 'greyhounds',
		'h' => 'harness',
		't'	=> 'galloping'
	);

	/**
	 * Load a single record from the tbdb_meeting table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getMeeting($id)
	{
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
	}
   
    
   
     /**
	 * Load a single record from the tbdb_meeting table by ID for Api.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getMeetingApi($id)
	{
		$db =& $this->getDBO();
		$query = "SELECT eg.id, eg.external_event_group_id, eg.wagering_api_id, eg.name, eg.tournament_competition_id, eg.start_date, eg.display_flag, eg.created_date, eg.updated_date, eg.meeting_code, eg.state, eg.events, eg.track, eg.weather, eg.type_code FROM `tbdb_event_group` AS eg WHERE eg.id ='".$id."'";
		//$query = "SELECT * FROM `#__meeting` WHERE `id`=".$id;
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Check the races for a meeting to determine if it has been abandoned, meaning
	 * that greater than 50% of the races have been abandoned.
	 * 
	 * @deprecated
	 * @param integer $id
	 * @return bool
	 */
	public function isMeetingAbandoned($id)
	{
		return $this->isEventGroupAbandoned($id);
	}

	/**
	 * Load a racing meeting record by meeting code and date
	 *
	 * @param string $meeting_code
	 * @param date $meeting_date
	 * @return object
	 */

	public function getMeetingByMeetingCodeAndDate($meeting_code, $meeting_date)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('meeting_code', $meeting_code),
			SuperModel::getFinderCriteria('start_date', $meeting_date)
		),  SuperModel::FINDER_SINGLE);
	}

	/**
	 * Get a list of future meetings
	 *
	 * @return array
	 */
	public function getMeetingUpcomingList()
	{
		$table = $this->_getTable();
		$table->addFunctionWhere('start_date', 'NOW()',
									DatabaseQueryTableWhere::CONTEXT_AND,
									DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL);

		$table->addOrder('start_date', DatabaseQueryTableOrder::ASCENDING);

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Get a list of future meetings by meeting type
	 *
	 * @return array
	 */
	public function getMeetingUpcomingListByMeetingTypeID($meeting_type_id)
	{
		return $this->find(array(SuperModel::getFinderCriteria('tournament_competition_id', $meeting_type_id)));
	}

	/**
	 * Get a list of meetings which are being used in tournaments
	 *
	 * @return array
	 */
	public function getActiveMeetingList()
	{
		$join = new DatabaseQueryTable($this->_table_name);
		$join->addGroup('event_group_id');

		$table = $this->_getTable();
		$table->addFunctionWhere('start_date', 'NOW()',
									DatabaseQueryTableWhere::CONTEXT_AND,
									DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL);

		$table->addOrder('updated_date');
		$table->addJoin($join,'id', 'event_group_id');

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Get a list of meetings which are being used in tournaments
	 *
	 * @param int $meeting_type_id
	 * @return array
	 */
	public function getActiveMeetingListByMeetingTypeID($meeting_type_id)
	{
		$table = $this->_getTable()
						->addWhere('tournament_competition_id', $meeting_type_id)
						->addOrder('start_date');

		$tournament = new DatabaseQueryTable('#__tournament');
		$tournament->addFunctionWhere('end_date', 'NOW()',
						DatabaseQueryTableWhere::CONTEXT_AND,
						DatabaseQueryTableWhere::OPERATOR_GREATER_THAN);

		$map = new DatabaseQueryTable($this->_table_name);
		$map->addGroup('event_group_id')
			->addJoin($tournament, 'tournament_id', 'id');

		$table->addJoin($map, 'id', 'event_group_id');
		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList('id'));
	}

	/**
	 * Get meeting start and end dates based on the races
	 *
	 * @param int $meeting_id
	 * @return array
	 */
	public function getMeetingFirstAndLastRaceTimeByMeetingID($meeting_id)
	{
		return $this->getEventGroupFirstAndLastEventTimeByEventGroupID($meeting_id);
	}

	public function getCompetitionNameFromImportType($type_code)
	{
		$name 		= false;
		$type_code 	= strtolower($type_code);

		if(array_key_exists($type_code, $this->type_list)) {
			$name = $this->type_list[$type_code];
		}

		return $name;
	}
	
	
	/**
	 * Get a list of today's active meetings
	 *
	 * @return array
	 */
	public function getTodayActiveMeetingList()
	{
//		$table				= $this->_getTable();
//		$competition_table	= new DatabaseQueryTable('#__tournament_competition');
//		$competition_table->addColumn(new DatabaseQueryTableColumn('name', 'competition_name'));
//		
//		$event_group_event_table	= new DatabaseQueryTable('#__event_group_event');
//		
//		$event_table	= new DatabaseQueryTable('#__event');
//		$event_table	->addColumn(new DatabaseQueryTableColumn('start_date', 'event_start_date'))
//						->addFunctionWhere('start_date', DatabaseQueryHelperFunction::NOW,
//								DatabaseQueryTableWhere::CONTEXT_AND,
//								DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL)
//						->addOrder('start_date'); 
//						
//		$event_group_event_table	->addJoin($event_table,'event_id', 'id')
//									->addGroup('event_group_id');
//									
//		$table	->addJoin($competition_table, 'tournament_competition_id', 'id')
//				->addJoin($event_group_event_table, 'id', 'event_group_id');
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
//		return $db->loadObjectList();

		$db =& $this->getDBO();
		$query = '
			SELECT
				eg.id,
				eg.external_event_group_id,
				eg.wagering_api_id,
				eg.name,
				eg.tournament_competition_id,
				eg.start_date,
				eg.display_flag,
				eg.created_date,
				eg.updated_date,
				eg.meeting_code,
				case when ms.name > \'\' then ms.name else \'-\' end as state,
				eg.events,
				eg.track,
				eg.weather,
				eg.type_code,
				tc.name AS competition_name,
				e.start_date AS event_start_date
			FROM
				' . $db->nameQuote('#__event_group') . ' AS eg
			INNER JOIN
				'. $db->nameQuote('#__tournament_competition') . ' AS tc
			ON
				eg.tournament_competition_id = tc.id
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS ts
			ON
				ts.id = tc.tournament_sport_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				ege.event_id = e.id
			LEFT JOIN
				' . $db->nameQuote('#__meeting_venue') . ' AS mv
			ON
				eg.name = mv.name
			LEFT JOIN
				' .  $db->nameQuote('#__meeting_state') . ' AS ms
			ON
				mv.meeting_state_id = ms.id
			WHERE
				eg.display_flag = 1
			AND
				e.start_date >= date(now())
			AND
				LOWER(ts.name) IN ("galloping", "harness", "greyhounds")
			GROUP BY
				ege.event_group_id
			ORDER BY
				e.start_date ASC
		';
		$db->setQuery($query);
		return $this->_loadModelList($db->loadObjectList());
	}
	
	public function getMeetingByEventId($event_id){
	
	$table = $this->_getTable();
	
	$event_group_map = new DatabaseQueryTable('#__event_group_event');
			$event_group_map->addWhere('event_id', $event_id);
	
	$event_group = new DatabaseQueryTable($this->_table_name);
	
	$table->addJoin($event_group_map, 'id', 'event_group_id');
			
			$db =& $this->getDBO();
	
	$query = new DatabaseQuery($table);
	$db->setQuery($query->getSelect());
	
	return $this->_loadModel($db->loadObject());
	}
	
	/**
	* Load Meeting Record by Race ID
	*
	* @param integer $race_id
	* @return object
	*/
	public function getMeetingByRaceID($race_id)
	{
		$table				= $this->_getTable();
		$competition_table	= new DatabaseQueryTable('#__tournament_competition');
		$competition_table 	->addColumn(new DatabaseQueryTableColumn('name', 'competition_name'));
	
		$event_group_event_table	= new DatabaseQueryTable('#__event_group_event');
	
		$event_table	= new DatabaseQueryTable('#__event');
	
		$event_group_event_table	->addWhere('event_id', $race_id)
		->addJoin($event_table,'event_id', 'id')
		->addGroup('event_group_id');
			
		$table	->addJoin($competition_table, 'tournament_competition_id', 'id')
		->addJoin($event_group_event_table, 'id', 'event_group_id');
	
		$db =& $this->getDBO();
	
		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());
	
		return $db->loadObject();
	}
	
	/**
	* Load Meeting Record by Meeting Name and Date
	*
	* @param string $meeting_name
	* @param string $type_code
	* @param string $date_time_stamp
	* @return object
	*/
	public function getMeetingByNameAndTypeCodeAndDate($meeting_name, $type_code, $date_time_stamp)
	{
		$db =& $this->getDBO();
		
		$query ='
			SELECT
				*
			FROM
				' . $db->nameQuote('#__event_group') . '
			WHERE
				name = ' . $db->quote($meeting_name) . '
			AND
				type_code = ' . $db->quote($type_code) . '
			AND
				date(start_date) = ' . $db->quote(date('Y-m-d', $date_time_stamp))
		;
		
		$db->setQuery($query);
	
		return  $this->_loadModel($db->loadObject());
	}
}
