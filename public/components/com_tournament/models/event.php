<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

/**
 * tournament match Model
 */
class TournamentModelEvent extends SuperModel
{
	const STATUS_PAYING = 'paying';
	const STATUS_SELLING = 'selling';
	const STATUS_ABANDONED = 'abandoned';
	const STATUS_PAID = 'paid';

	protected $_table_name = '#__event';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'event_id' => array(
			'name' => 'Internal Event ID',
			'type' => self::TYPE_INTEGER,
		),
		'tournament_competition_id' => array(
			'name' => 'Tournament Competition ID',
			'type' => self::TYPE_INTEGER
		),
		'external_event_id' => array(
			'name' => 'External event ID',
			'type' => self::TYPE_STRING
		),
		'wagering_api_id' => array(
			'name' => 'Wagering Api ID',
			'type' => self::TYPE_INTEGER
		),
		'event_status_id' => array(
			'name' => 'Event Status ID',
			'type' => self::TYPE_INTEGER
		),
		'paid_flag' => array(
			'name' => 'Paid Flag',
			'type' => self::TYPE_INTEGER
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'start_date' => array(
			'name' => 'Start Date',
			'type' => self::TYPE_DATETIME
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		),
		'distance' => array(
			'name' => 'Distance',
			'type' => self::TYPE_STRING
		),
		'weather' => array(
			'name' => 'Weather',
			'type' => self::TYPE_STRING
		),
		'track_condition' => array(
			'name' => 'Track Condition',
			'type' => self::TYPE_STRING
		)
	);

	/**
	 * Get a single tournament Match record by Match ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getEvent($id)
	{
		return $this->load($id);
	}

	public function getEventListByEventGroupID($event_group_id)
	{
		$ege_table = new DatabaseQueryTable('#__event_group_event');
		$ege_table->addWhere('event_group_id', $event_group_id);

		$table = $this->_getTable();
		$table	->addJoin($ege_table, 'id', 'event_id')
				->addOrder('start_date');

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $this->_loadModelList($db->loadObjectList('id'));
	}

	/**
	 * Get a single tournament Match record by Match External meeting ID & External Match ID.
	 *
	 * @param integer $ext_meeting_id
	 * @param integer $ext_match_id
	 * @return object
	 */
	public function getEventByExternalEventID($ext_event_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('external_event_id', $ext_event_id)
		), 	SuperModel::FINDER_SINGLE);
	}
	
	/**
	* Get a single tournament Match record by External event id and wagering api id
	*
	* @param integer $ext_group_id
	* @param integer $wagering_api_id
	* @return object
	*/
	public function getEventByExternalEventIDAndWageringApiID($ext_group_id,  $wagering_api_id)
	{
		return $this->find(array(
		SuperModel::getFinderCriteria('external_event_id', $ext_group_id),
		SuperModel::getFinderCriteria('wagering_api_id', $wagering_api_id)
		), 	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Get a single tournament Match record by external meeting ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getEventListByExternalID($id)
	{
		return $this->find(array(SuperModel::getFinderCriteria('external_meeting_id', $id)));
	}

	/**
	 * Get a tournament Match records by Event ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getEventListByCompetitionID($id)
	{
		return $this->find(array(SuperModel::getFinderCriteria('tournament_competition_id', $id)));
	}

	/**
	 * Get future tournament match records
	 *
	 * @return object
	 *
	 */
	public function getUpcomingEventList()
	{
		$table = $this->_getTable()->addFunctionWhere('start_date', 'NOW()',
														DatabaseQueryTableWhere::CONTEXT_AND,
														DatabaseQueryTableWhere::OPERATOR_GREATER_THAN);

		$db =& $this->getDBO();

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Get list of matches by current status
	 * @param string $status
	 * @return object
	 */
	private function _getEventListByStatus($status)
	{

//		//first join trail
//		$sport = new DatabaseQueryTable('#__tournament_sport');
//		$sport->addColumn('racing_flag');
//		
//		$competition = new DatabaseQueryTable('#__tournament_competition');
//		$competition->addJoin($sport, 'tournament_sport_id', 'id');
//		
//		$event_group = new DatabaseQueryTable('#__event_group');
//		$event_group->addJoin($competition, 'tournament_competition_id', 'id');
//		
//		$event_group_event = new DatabaseQueryTable('#__event_group_event');
//		$event_group_event->addJoin($event_group, 'event_group_id' , 'id')
//			->addColumn('event_group_id');
//		
//		$status_table = new DatabaseQueryTable('#__event_status');
//		$status_table->addWhere('keyword', $status);
//
//		$event = $this->_getTable()
//			->addJoin($status_table, 'event_status_id', 'id')
//			->addJoin($event_group_event, 'id', 'event_id')
//			->addWhere('paid_flag', 0);
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($event);
//		$db->setQuery($query->getSelect());

		//TODO: fix super model addFunctionWhere and IN function
		$db =& $this->getDBO();
		if (!is_array($status)) {
			$status = array($status);
		}
		
		$status_clean = array();
		foreach ($status as $s) {
			$status_clean[] = $db->quote($s);
		}
		$status_clean = implode(', ', $status_clean);
		
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
				ege.event_group_id,
				ts.racing_flag
			FROM
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_status') . ' AS es
			ON
				e.event_status_id = es.id
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				ege.event_group_id = eg.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS tc
			ON
				eg.tournament_competition_id = tc.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS ts
			ON
				tc.tournament_sport_id = ts.id
			WHERE
				e.paid_flag = 0
			AND
				es.keyword IN (' . $status_clean . ')';
		$db->setQuery($query);
		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Get a list of paying events
	 *
	 * @return object
	 */
	public function getPayingEventList($include_paid = false)
	{
		if ($include_paid) {
			return $this->_getEventListByStatus(array(self::STATUS_PAYING, self::STATUS_PAID));
		} else {
			return $this->_getEventListByStatus(self::STATUS_PAYING);
		}
	}

	/**
	 * Get a list of abandoned matches
	 *
	 * @return object
	 */
	public function getAbandonedEventList()
	{
		return $this->_getEventListByStatus(self::STATUS_ABANDONED);
	}

	/**
	 * Get Tournament Match List
	 * the list can be filtered by Sport ID or by Sport ID & Competition ID
	 */
	public function getEventListBySportIDAndCompetitionID($sport_id = null, $competition_id = null, $params = array())
	{
		$db = $this->getDBO();
		
		$order		= isset($params['order']) ? $params['order'] : 'name';
		$direction	= isset($params['direction']) ? $params['direction'] : 'ASC';
		$limit		= isset($params['limit']) ? $params['limit'] : null;
		$offset		= isset($params['offset']) ? $params['offset'] : null;
		$type		= isset($params['type']) ? $params['type'] : null;
		
		if($direction != 'ASC' && $direction != 'DESC')
		{
			$direction = 'ASC';
		}
		
//		if(is_null($order)) {
//			$order = (empty($this->order)) ? 'name' : $this->order;
//		}
//
//		if(preg_match('/^[a-z0-9]+\./', $order)) {
//			$order = substr(strrchr($order, '.'), 1);
//		}
//
//		if(is_null($direction)) {
//			$direction = (empty($this->direction)) ? 'ASC' : $this->direction;
//		}
//
//		if(!is_int($direction)) {
//			$direction = (strtolower($direction) == 'asc') ? DatabaseQueryTableOrder::ASCENDING : DatabaseQueryTableOrder::DESCENDING;
//		}
//
//		if(is_null($limit)) {
//			$limit = (empty($this->limit)) ? 0 : $this->limit;
//		}
//
//		if(is_null($offset)) {
//			$offset = (empty($this->offset)) ? 0 : $this->offset;
//		}
//
//		$competition_table = new DatabaseQueryTable('#__tournament_competition');
//		$competition_table	->addColumn(new DatabaseQueryTableColumn('name', 'competition_name'))
//							->addColumn('tournament_sport_id')
//							->addColumn('external_competition_id');
//
//		$sport_table = new DatabaseQueryTable('#__tournament_sport');
//		$sport_table->addColumn(new DatabaseQueryTableColumn('name', 'sport_name'));
//
//		$main_table = $this->_getTable();
//
//		if($order == 'competition_name') {
//			$competition_table->addOrder('name');
//		} elseif($order == 'sport_name') {
//			$sport_table->addOrder('name');
//		} else {
//			$main_table->addOrder($order);
//		}
//
//		if($sport_id > 0) {
//			$sport_table->addWhere('id', $sport_id);
//			if($competition_id > 0){
//				$competition_table->addWhere('id', $competition_id);
//			}
//		}
//
//		$main_table->addJoin($competition_table->addJoin($sport_table, 'tournament_sport_id', 'id'), 'tournament_competition_id', 'id');
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($main_table);
//		$db->setQuery($query->getSelect(), $offset, $limit);
//
//		return $this->_loadModelList($db->loadObjectList());

		$query = '
			SELECT
				e.id,
				e.tournament_competition_id,
				e.external_event_id,
				e.wagering_api_id,
				e.name,
				e.event_status_id,
				e.paid_flag,
				e.start_date,
				tc.name AS competition_name,
				ts.name AS sport_name,
				e.created_date,
				e.updated_date
				
			From
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				ege.event_id = e.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS tc
			ON
				tc.id = eg.tournament_competition_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS ts
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
			GROUP BY
				e.id
			ORDER BY ' . $db->nameQuote($order) . ' ' . $direction
		;
		
	    $db->setQuery($query, $offset, $limit);
	    return $this->_loadModelList($db->loadObjectList());
	}

	/**
	* Count the total number of matches
	*
	* @return integer
	*/
	public function getTotalEventCount($sport_id = null, $competition_id = null, $params = array())
	{
		$db = $this->getDBO();
//
//		$table = new DatabaseQueryTable('#__event');
//		$table->addFunction('id');
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
//
//		return $db->loadResult();

		$type		= isset($params['type']) ? $params['type'] : null;
		
		$query = '
			SELECT
				COUNT(e.id)
			From
				' . $db->nameQuote('#__event') . ' AS e
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				ege.event_id = e.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS tc
			ON
				tc.id = eg.tournament_competition_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS ts
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
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Set match by ID to paying status
	 * @param int $id
	 * @return void
	 */
	public function setEventToPaying($id)
	{
		$this->_setEventStatus($id,self::STATUS_PAYING);
	}

	/**
	 * Set match by ID to paying status
	 * @param int $id
	 * @return void
	 */
	public function setEventToAbandoned($id)
	{
		$this->_setEventStatus($id, self::STATUS_ABANDONED);
	}

	/**
	 * Set match to paid - paid flag and status
	 * @param int $id
	 * @return void
	 */
	public function setEventToPaid($id)
	{
		$this->_setEventStatus($id, self::STATUS_PAID);
	}

	/**
	 * Set match paid flag for abadoned match
	 * @param int $id
	 * @return void
	 */
	public function setAbandonedEventToPaid($id)
	{
		$match = $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);

		$match->paid_flag = 1;
		return $match->save();
	}

	/**
	 * Set match status
	 * @param int $match_id
	 * @param string $status
	 * @return void
	 */
	private function _setEventStatus($id, $status)
	{
		$match 				= $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
		$match_status		=& JModel::getInstance('EventStatus', 'TournamentModel');
		$match_status_id 	= $match_status->getEventStatusByKeyword($status)->id;

		$match->event_status_id = $match_status_id;
		if($status == self::STATUS_PAID){
			$match->paid_flag = 1;
		}

		return $match->save();
	}

	/**
	 * check if match has been resulted
	 *
	 * @param int $id
	 * @return void
	 */
	public function isEventPaying($id)
	{
		$matchstatus = $this->_getEventStatus($id);

		if($matchstatus == self::STATUS_PAYING){
			return true;
		}

		return false;
	}

	/**
	 * check if match has been abandoned
	 *
	 * @param int $id
	 * @return void
	 */
	public function isEventAbandoned($id)
	{
		$matchstatus = $this->_getEventStatus($id);

		if($matchstatus == self::STATUS_ABANDONED){
			return true;
		}

		return false;
	}

	/**
	 * check if match has been resulted
	 *
	 * @param int $id
	 * @return void
	 */
	public function isEventResulted($id)
	{
		$matchstatus = $this->_getEventStatus($id);

		$resulted_status_list = array(
			self::STATUS_PAYING,
			self::STATUS_ABANDONED,
			self::STATUS_PAID
		);

		if(in_array($matchstatus, $resulted_status_list)) {
			return true;
		}

		return false;
	}

	/**
	 * Get the last event by event group. Useful for calculating end dates.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getFinalEventByEventGroupID($id)
	{
		$map_table = new DatabaseQueryTable('#__event_group_event');
		$map_table->addWhere('event_group_id', $id);

		$table = $this->_getTable()
					->addJoin($map_table, 'id', 'event_id')
					->addOrder('start_date', DatabaseQueryTableOrder::DESCENDING);

		$query = new DatabaseQuery($table, 1);
		$db =& $this->getDBO();

		$db->setQuery($query->getSelect());
		return $this->_loadModel($db->loadObject());
	}

	/**
	 * Get match status
	 *
	 * @param int $id
	 * @return string
	 */
	private function _getEventStatus($id)
	{
		$match = $this->getEvent($id);
		
		$matchstatus_model	 =& JModel::getInstance('EventStatus', 'TournamentModel');
		$matchstatus = $matchstatus_model->getEventStatus($match->event_status_id);

		return $matchstatus->keyword;
	}
	
	public function getNextEventByEventGroupID($event_group_id)
	{
		$db =& $this->getDBO();
		$query =
				'SELECT
					e.id,
					e.tournament_competition_id,
					e.external_event_id,
					e.name,
					e.start_date,
					e.event_status_id,
					e.paid_flag
				FROM
					' . $db->nameQuote( '#__event' ) . ' AS e
				INNER JOIN
					' . $db->nameQuote( '#__event_group_event' ) . ' AS ege
				ON
					ege.event_id = e.id
				WHERE
					ege.event_group_id = ' . $db->quote($event_group_id) . '
				AND e.start_date > NOW()
			ORDER BY
				e.start_date, e.name
			LIMIT 1
		';
		$db->setQuery($query);
		return $db->loadObject();
	}
}
