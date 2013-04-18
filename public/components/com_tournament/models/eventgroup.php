<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.factory');

/**
 * tournament event Model
 */
class TournamentModelEventGroup extends SuperModel
{
	protected $_table_name = '#__event_group';

	protected $_member_list = array(
		'id' => array(
			'name' 		=> 'ID',
			'primary' 	=> true,
			'type'		=> self::TYPE_INTEGER
		),
		'external_event_group_id' => array(
			'name' 		=> 'External ID',
			'type' 		=> self::TYPE_STRING
		),
		'wagering_api_id' => array(
			'name' 		=> 'Wagering Api ID',
			'type' 		=> self::TYPE_INTEGER
		),
		'name' => array(
			'name' 		=> 'Name',
			'type'		=> self::TYPE_STRING
		),
		'tournament_competition_id' => array(
			'name' 		=> 'Tournament Competition ID',
			'type'		=> self::TYPE_INTEGER
		),
		'start_date' => array(
			'name' 		=> 'Start Date',
			'type'		=> self::TYPE_DATETIME
		),
		'display_flag' => array(
			'name' 		=> 'Display',
			'type' 		=> self::TYPE_INTEGER
		),
		'created_date' => array(
			'name' 		=> 'Created Date',
			'type'		=> self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' 		=> 'Updated Date',
			'type'		=> self::TYPE_DATETIME_UPDATED
		)
	);

	/**
   	* Get a single tournament event group record by ID.
   	*
   	* @param integer $id
   	* @return object
   	*/
	public function getEventGroup($id)
	{
		return $this->load($id);
  	}

  	/**
  	 * Get Tournament event by name
  	 * @param string $name
  	 */
	public function getEventGroupByName($name)
	{
		return $this->find(array(
			self::getFinderCriteria('name', $name)
		),
		self::FINDER_SINGLE);
  	}
  	
  	/**
  	 * Get Tournament event by external_event_group_id
  	 * @param string $id
  	 */
	public function getEventGroupByExternalEventGroupIdAndWageringApiId($id, $waagering_api_id) {
		return $this->find(array(
			self::getFinderCriteria('external_event_group_id', $id),
			self::getFinderCriteria('wagering_api_id', $waagering_api_id)
		),
		self::FINDER_SINGLE);
  	}
  	

	/**
  	 * Get Tournament event by name
  	 * @param string $name
  	 */
	public function getEventGroupListByCompetitionID($competition_id)
	{
		$table = $this->_getTable();

		$table	->addWhere('tournament_competition_id', $competition_id)
				->addOrder('name', DatabaseQueryTableOrder::ASCENDING);

		$db =& JFactory::getDBO();

		$query = new DatabaseQuery($table);
	    $db->setQuery($query->getSelect());

	    return $this->_loadModelList($db->loadObjectList());
  	}

	/**
  	 * Get Tournament event by name
  	 * @param string $name
  	 */
	public function getActiveEventGroupListByCompetitonID($competition_id)
	{
//		$join = new DatabaseQueryTable('#__tournament_event_group');
//		$join->addGroup('event_group_id');

		$table = $this->_getTable();

		$table	->addWhere('tournament_competition_id', $competition_id)
				->addWhere('display_flag', 1)
				->addFunctionWhere('start_date', DatabaseQueryHelperFunction::CURDATE,
									DatabaseQueryTableWhere::CONTEXT_AND,
									DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL)

				->addOrder('start_date', DatabaseQueryTableOrder::ASCENDING);
//				->addJoin($join, 'id', 'event_group_id', DatabaseQueryTableJoin::LEFT);

		$db =& JFactory::getDBO();

		$query = new DatabaseQuery($table);
	    $db->setQuery($query->getSelect());

	    return $this->_loadModelList($db->loadObjectList('id'));
  	}

  	/**
  	 * get event start time
  	 */
	public function getEventGroupStartTimeByEventID($id)
	{
		$match_table = new DatabaseQueryTable('#__event');
		$match_table->addColumn('start_date')
					->addOrder('start_date');

		$map_table = new DatabaseQueryTable('#__event_group_event');
		$map_table->addJoin($match_table, 'event_id', 'id');

		$event_table = new DatabaseQueryTable('#__event_group');
		$event_table->addWhere('id', $id)
					->addJoin($map_table, 'id', 'event_group_id');

		$db =& JFactory::getDBO();

		$query = new DatabaseQuery($event_table, 1);
		$db->setQuery($query->getSelect());

	    return $db->loadResult();
  	}
  	


  	public function getEventGroupListByEventId($event_id)
  	{
	    $db =& JFactory::getDBO();
	    
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
				eg.updated_date
			FROM
				' . $db->nameQuote('#__event_group') . ' AS eg
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				ege.event_group_id = eg.id
			WHERE
				ege.event_id = ' . $db->quote($event_id);
		
		$db->setQuery($query);
	    return $this->_loadModelList($db->loadObjectList()); 
  	}
  	
  	
  	/**
  	 * Get Tournament Event List
  	 * the list can be filtered by Sport ID or by Sport ID & Competition ID
  	 */
	public function getEventGroupListBySportIDAndCompetitionID($sport_id, $competition_id, $params = array())
	{
//		$event_table = $this->_getTable();
//
//		$competition_table = new DatabaseQueryTable('#__tournament_competition');
//		$competition_table	->addColumn(new DatabaseQueryTableColumn('name', 'competition_name'))
//							->addColumn('tournament_sport_id')
//							->addColumn('external_competition_id');
//
//		$sport_table = new DatabaseQueryTable('#__tournament_sport');
//		$sport_table->addColumn('name', 'sport_name');
//		
//		
//		if(!is_null($order)) {
//	    	$order = (empty($this->order)) ? 'name' : $this->order;
//
//		  	if(is_null($direction)) {
//				$direction = (empty($this->direction)) ? DatabaseQueryTableOrder::ASCENDING : $this->direction;
//
//		  		if(!is_int($direction)) {
//		  			$direction = (strtolower($direction) == 'desc') ? DatabaseQueryTableOrder::DESCENDING : DatabaseQueryTableOrder::ASCENDING;
//		  		}
//		  	}
//
//	    	if($order == 'sport_name') {
//	    		$sport_table->addOrder('name', $direction);
//	    	} else if($order == 'competition_name') {
//	    		$competition_table->addOrder('name', $direction);
//	    	} else {
//	    		$event_table->addOrder($order, $direction);
//	    	}
//	  	}
//
//	  	if(is_null($limit)) {
//		  	$limit = (empty($this->limit)) ? 0 : $this->limit;
//	  	}
//
//	  	if(is_null($offset)) {
//			$offset = (empty($this->offset)) ? 0 : $this->offset;
//	  	}
//
//		$db =& JFactory::getDBO();
//
//		if($sport_id > 0) {
//			$sport_table->addWhere('id', $sport_id);
//
//			if($competition_id > 0) {
//				$competition_table->addWhere('id', $competition_id);
//			}
//		}
//
//		$competition_table->addJoin($sport_table, 'tournament_sport_id', 'id');
//		$event_table->addJoin($competition_table, 'tournament_competition_id', 'id');
//
//		$query = new DatabaseQuery($event_table, $limit, $offset);
//		$db->setQuery($query->getSelect());
//		print($db->_sql);exit;
//
//	    return $this->_loadModelList($db->loadObjectList());

	    $db =& JFactory::getDBO();
	    
		$order		= isset($params['order']) ? $params['order'] : 'name';
		$direction	= isset($params['direction']) ? $params['direction'] : 'ASC';
		$limit		= isset($params['limit']) ? $params['limit'] : null;
		$offset		= isset($params['offset']) ? $params['offset'] : null;
		$type		= isset($params['type']) ? $params['type'] : null; 
		
		if($direction != 'ASC' && $direction != 'DESC')
		{
			$direction = 'ASC';
		}
		
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
				tc.name AS competition_name,
				tc.tournament_sport_id,
				tc.external_competition_id,
				ts.name AS sport_name
			FROM
				' . $db->nameQuote('#__event_group') . ' AS eg
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS tc
			ON
				eg.tournament_competition_id = tc.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . 'AS ts
			ON
				tc.tournament_sport_id = ts.id
		
		';
		
		$where = array();
		if ($sport_id > 0) {
			$where[] = 'ts.id = ' . $db->quote($sport_id);
			
			if ($competition_id > 0) {
				$where[] = 'tc.id = ' . $db->quote($competition_id);
			}
		}
		
		switch ($type) {
			case 'sports':
				$where[] = 'LOWER(ts.name) NOT IN ("galloping","harness","greyhounds")';
				break;
			case 'racing':
				$where[] = 'LOWER(ts.name) IN ("galloping","harness","greyhounds")';
				break;
		}
		
		if (count($where) > 0) {
			$where = implode(' AND ', $where);
			$query .= '
				WHERE ' . $where;
		}
		
		$query .= '
			ORDER BY
				' . $db->nameQuote($order) . ' ' . $direction
		;
	
	    $db->setQuery($query, $offset, $limit);
	    return $this->_loadModelList($db->loadObjectList());
  	}
  	/**
  	 * Count Events
  	 */
  	public function getTotalEventGroupCountBySportAndCompetitionID($sport_id, $competition_id, $type = null)
  	{
//  		$event_table = new DatabaseQueryTable($this->_table_name);
//  		$event_table->addFunction('id', 'total_events');
//
//  		$sport_table = new DatabaseQueryTable('#__tournament_sport');
//  		if($sport_id > 0) {
//  			$sport_table->addWhere('id', $sport_id);
//  		}
//
//  		$comp_table = new DatabaseQueryTable('#__tournament_competition');
//  		if($competition_id > 0) {
//  			$comp_table->addWhere('id', $competition_id);
//  		}
//
//  		$event_table->addJoin($comp_table, 'tournament_competition_id', 'id');
//  		$event_table->addJoin($sport_table, 'tournament_competition_id', 'id');
//
//	  	$db =& JFactory::getDBO();
//
//	  	$query = new DatabaseQuery($event_table);
//		$db->setQuery($query->getSelect());
//
//	    return $db->loadResult();

  		$db =& JFactory::getDBO();
  		
		$query = '
			SELECT
				count(eg.id)
			FROM
				' . $db->nameQuote('#__event_group') . ' AS eg
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS tc
			ON
				eg.tournament_competition_id = tc.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . 'AS ts
			ON
				tc.tournament_sport_id = ts.id
		
		';
		
  		$where = array();
		if ($sport_id > 0) {
			$where[] = 'ts.id = ' . $db->quote($sport_id);
			
			if ($competition_id > 0) {
				$where[] = 'tc.id = ' . $db->quote($competition_id);
			}
		}
		
		switch ($type) {
			case 'sports':
				$where[] = 'ts.name NOT IN ("galloping","harness","greyhounds")';
				break;
			case 'racing':
				$where[] = 'ts.name IN ("galloping","harness","greyhounds")';
				break;
		}
		
		if (count($where) > 0) {
			$where = implode(' AND ', $where);
			$query .= '
				WHERE ' . $where;
		}
		
		$db->setQuery($query);
		return $db->loadResult();
  	}
  	
  	/**
  	 * The start & end time of the event is based on the matchs
  	 * the 1st match start time is the Start time & the last match will be used for bet closing & tournament end time
  	 *
  	 */
  	public function getEventGroupFirstAndLastEventTimeByEventGroupID($id)
  	{
//  		$event_table_1 	= new DatabaseQueryTable($this->_table_name);
//  		$event_table_2 	= new DatabaseQueryTable($this->_table_name);
//
//  		$map_table_1	= new DatabaseQueryTable('#__event_group_event');
//  		$map_table_2	= new DatabaseQueryTable('#__event_group_event');
//
//  		$match_table_1	= new DatabaseQueryTable('#__event');
//  		$match_table_2	= new DatabaseQueryTable('#__event');
//
//  		$match_table_1->addColumn('start_date');
//  		$match_table_2->addColumn('start_date');
//
//  		$match_table_1->addOrder('start_date', DatabaseQueryTableOrder::ASCENDING);
//  		$match_table_2->addOrder('start_date', DatabaseQueryTableOrder::DESCENDING);
//
//  		$event_table_1->addWhere('id', $id);
//  		$event_table_2->addWhere('id', $id);
//
//  		$map_table_1->addJoin($match_table_1, 'event_id', 'id');
//  		$map_table_2->addJoin($match_table_2, 'event_id', 'id');
//
//  		$event_table_1->addJoin($map_table_1, 'id', 'event_group_id');
//  		$event_table_2->addJoin($map_table_2, 'id', 'event_group_id');
//
//  		$query_1 = new DatabaseQuery($event_table_1, 1);
//  		$query_2 = new DatabaseQuery($event_table_2, 1);
//
//  		$select_1 = $query_1->getSelect();
//  		$select_2 = $query_2->getSelect();
//
//  		$virtual_table = new DatabaseQueryTable(null);
//
//  		$virtual_table->addSubquery("({$select_1})", 'first_match_time');
//  		$virtual_table->addSubquery("({$select_2})", 'last_match_time');
//
//  		$query = new DatabaseQuery($virtual_table);
//
//		$db =& JFactory::getDBO();
//		$db->setQuery($query->getSelect());
//
//	    return $this->_loadModel($db->loadObject());

		$db =& $this->getDBO();
		$query = '
			SELECT
				(SELECT
					e.start_date
				FROM
					' . $db->nameQuote('#__event_group') . ' AS eg
				INNER JOIN
					' . $db->nameQuote('#__event_group_event') . ' AS ege
				ON
					ege.event_group_id = eg.id
				INNER JOIN
					' . $db->nameQuote('#__event') . ' AS e
				ON
					e.id = ege.event_id
				WHERE
					eg.id = ' . $db->quote($id) . '
				ORDER BY
					e.start_date ASC LIMIT 0,1
				) AS first_match_time,
				(SELECT
					e.start_date
				FROM
					' . $db->nameQuote('#__event_group') . ' AS eg
				INNER JOIN
					' . $db->nameQuote('#__event_group_event') . ' AS ege
				ON
					ege.event_group_id = eg.id
				INNER JOIN
					' . $db->nameQuote('#__event') . ' AS e
				ON
					e.id = ege.event_id
				WHERE
					eg.id = ' . $db->quote($id) . '
				ORDER BY
					e.start_date DESC LIMIT 0,1
				) AS last_match_time
			';
				
		$db->setQuery($query);
		return $db->loadObject();
  	}

	/**
	 * Update event date with 1st macth date
	 */
	public function updateEventGroupDate($id, $date)
	{
//		$table = new DatabaseQueryTable($this->_table_name);
//
//		$table	->addColumn('start_date', $date)
//				->addFunction('updated_date', 'NOW()')
//				->addWhere('id', $id);
//
//		$db =& JFactory::getDBO();
//		$query = new DatabaseQuery($table);
//
//		$db->setQuery($query->getUpdate());
//		return $db->query();
		if(!empty($id) && !empty($date)){
			$db =& $this->getDBO();
			$query =
				'UPDATE
				' . $db->nameQuote('#__event_group') . '
				SET
					start_date = '. $db->quote($date) .',
					updated_date = NOW()
				WHERE
					id = ' . $db->quote($id);
			$db->setQuery($query);
			return $db->query();
		}
		return false;
	}
	
	/**
	* Check the races for a meeting to determine if it has been abandoned, meaning
	* that greater than 50% of the races have been abandoned.
	*
	* @param integer $id
	* @return bool
	*/
	public function isEventGroupAbandoned($id)
	{
		// XXX: needs cleanup - rush job
		$event = new DatabaseQueryTable('#__event');
		$event->addColumnFunction('id');
	
		$status_table = new DatabaseQueryTable('#__event_status');
		$status_table->addWhere('keyword', 'abandoned');
		
		$event_group = new DatabaseQueryTable('#__event_group_event');
		$event_group->addWhere('event_group_id', $id);
	
		$event->addJoin($status_table, 'event_status_id', 'id')
			->addJoin($event_group, 'id', 'event_id');
	
		$db =& $this->getDBO();
		
		$query = new DatabaseQuery($event);
		$db->setQuery($query->getSelect());

		$abandoned_count = $db->loadResult();
		
		$event = new DatabaseQueryTable('#__event');
		$event->addColumnFunction('id');
		
		$event_group = new DatabaseQueryTable('#__event_group_event');
		$event_group->addWhere('event_group_id', $id);
	
		$event->addJoin($event_group, 'id', 'event_id');
		
		$db =& $this->getDBO();
		
		$query = new DatabaseQuery($event);
		$db->setQuery($query->getSelect());
		$event_count = $db->loadResult();
		
		if(is_null($event_count)) {
			// race data probably isn't imported yet
			return false;
		}
		
		return (($event_count / 2) < $abandoned_count);
	}
	
	public function getActiveTournamentEventGroupList()
	{
		$db =& $this->getDBO();
		$query = '
			SELECT
				eg.id,
				eg.external_event_group_id,
				eg.wagering_api_id,
				eg.name,
				eg.meeting_code,
				eg.state,
				eg.events,
				eg.track,
				eg.weather,
				eg.type_code,
				eg.tournament_competition_id,
				eg.start_date,
				eg.display_flag,
				eg.created_date,
				eg.updated_date
			FROM
				' . $db->nameQuote('#__event_group') . ' AS eg
			LEFT JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.event_group_id = eg.id
			WHERE
				t.end_date > NOW()
			AND
				t.id IS NOT NULL
			GROUP BY
				eg.id
			ORDER BY
				eg.start_date ASC
		';
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}