<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

/**
 * Tournament Model
 *
 * @desc The model for the tournamnent table
 */
class TournamentModelTournament extends SuperModel
{
	protected $_table_name = '#__tournament';

	protected $_member_list = array(
		'id' => array(
			'name' 		=> 'ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary' 	=> true
		),
		'tournament_sport_id' => array(
			'name' 		=> 'Tournament Sport ID',
			'type' 		=> self::TYPE_INTEGER,
			'required' 	=> true
		),
		'parent_tournament_id' => array(
			'name' => 'Parent Tournament ID',
			'type' => self::TYPE_INTEGER
		),
		'event_group_id' => array(
			'name' 		=> 'Event Group ID',
			'type' 		=> self::TYPE_INTEGER,
			'required' 	=> true
		),
		'name' => array(
			'name' 		=> 'Name',
			'type' 		=> self::TYPE_STRING,
			'required' 	=> true
		),
		'description' => array(
			'name' 		=> 'Description',
			'type' 		=> self::TYPE_STRING,
			'required' 	=> true
		),
		'start_currency' => array(
			'name' 		=> 'Start Currency',
			'type' 		=> self::TYPE_INTEGER,
			'required' 	=> true
		),
		'start_date' => array(
			'name' 		=> 'Start Date',
			'type' 		=> self::TYPE_DATETIME,
			'required' 	=> true,
			'transform' => '_transformStartDate'
		),
		'end_date' => array(
			'name' 		=> 'End Date',
			'type' 		=> self::TYPE_DATETIME,
			'transform' => '_transformEndDate'
		),
		'jackpot_flag' => array(
			'name' 		=> 'Jackpot Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'buy_in' => array(
			'name' 		=> 'Buy-in',
			'type' 		=> self::TYPE_INTEGER,
			'transform' => '_transformBuyIn'
		),
		'entry_fee' => array(
			'name' 		=> 'Entry-fee',
			'type' 		=> self::TYPE_INTEGER,
			'transform' => '_transformEntryFee'
		),
		'minimum_prize_pool' => array(
			'name' 		=> 'Minimum Prize-pool',
			'type' 		=> self::TYPE_INTEGER
		),
		'paid_flag' => array(
			'name' 		=> 'Paid Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'auto_create_flag' => array(
			'name' 		=> 'Auto-create Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'cancelled_flag' => array(
			'name' 		=> 'Cancelled Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'cancelled_reason' => array(
			'name' 		=> 'Cancelled Reason',
			'type' 		=> self::TYPE_STRING
		),
		'private_flag' => array(
			'name' 		=> 'Private Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'status_flag' => array(
			'name' 		=> 'Status Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'closed_betting_on_first_match_flag' => array(
			'name' 		=> 'Betting closed on first match Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'betting_closed_date' => array(
			'name' 		=> 'Betting Closed Date',
			'type' 		=> self::TYPE_DATETIME
		),
		'reinvest_winnings_flag' => array(
			'name' 		=> 'Reinvest Winnings Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'bet_limit_flag' => array(
			'name' 		=> 'Bet Limit Flag',
			'type' 		=> self::TYPE_INTEGER
		),
		'created_date' => array(
			'name' 		=> 'Created Date',
			'type' 		=> self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' 		=> 'Updated Date',
			'type' 		=> self::TYPE_DATETIME_UPDATED
		),
		'tod_flag' => array(
			'name' 		=> 'Tournament of the day',
			'type' 		=> self::TYPE_STRING
		),
		'free_credit_flag' => array(
			'name' 		=> 'Free credit prize',
			'type' 		=> self::TYPE_INTEGER
		),
        'tournament_sponsor_name' => array(
            'name' 		=> 'Tournament sponsor name',
            'type' 		=> self::TYPE_STRING
        ),
        'tournament_sponsor_logo' => array(
            'name' 		=> 'Tournament sponsor logo',
            'type' 		=> self::TYPE_STRING
        ),
        'tournament_sponsor_logo_link' => array(
            'name' 		=> 'Tournament sponsor logo link',
            'type' 		=> self::TYPE_STRING
        ),
        'tournament_prize_format' => array(
            'name' 		=> 'Tournament Prize Format',
            'type' 		=> self::TYPE_INTEGER
        )
		//'feature_keyword' => array(
		//		'name' 		=> 'Tournament Feature',
		//		'type' 		=> self::TYPE_STRING
		//)
	);

	/**
	 * Get a single tournament record.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournament($id)
	{
		return $this->load($id);
	}

	public function getTournamentList($order = 'id', $direction = 'asc', $limit = 20, $offset = 0)
	{
		$dir = ($direction == 'asc') ? DatabaseQueryTableOrder::ASCENDING : DatabaseQueryTableOrder::DESCENDING;
		$table = $this->_getTable()->addOrder($order, $dir);

		$query = new DatabaseQuery($table, $limit, $offset);
		$db =& $this->getDBO();

		$db->setQuery($query->getSelect());
		return $this->_loadModelList($db->loadObjectList());
	}

	public function getTournamentAdminList($params = array())
	{
		$keyword		= isset($params['keyword']) ? $params['keyword'] : null;
		$private_flag	= isset($params['private_flag']) ? $params['private_flag'] : null;
		$start_date		= isset($params['start_date']) ? $params['start_date'] : null;
		$end_date		= isset($params['end_date']) ? $params['end_date'] : null;
		
		$order			= isset($params['order']) ? $params['order'] : 't.id';
		$direction		= isset($params['direction']) ? $params['direction'] : null;
		$limit			= isset($params['limit']) ? $params['limit'] : null;
		$offset			= isset($params['offset']) ? $params['offset'] : null;
		
//		$dir = ($direction == 'asc') ? DatabaseQueryTableOrder::ASCENDING : DatabaseQueryTableOrder::DESCENDING;

//		$sport = new DatabaseQueryTable('#__tournament_sport');
//		$sport->addColumn(new DatabaseQueryTableColumn('name', 'sport_name'));

//		if ($order == 'sport_name') {
//			$sport->addOrder('name', $dir);
//		}
//
//		$event_group = new DatabaseQueryTable('#__event_group');
//		$event_group->addColumn(new DatabaseQueryTableColumn('name', 'event_group_name'));
//
//		if ($order == 'event_group_name') {
//			$event_group->addOrder('name', $dir);
//		}
//
//		$parent = new DatabaseQueryTable('#__tournament');
//		$parent->addColumn(new DatabaseQueryTableColumn('name', 'parent_name'));
//
//		if ($order == 'parent_name') {
//			$parent->addOrder('name', $dir);
//		}
//		
//		if ($order == 'prize_formula' || $order == 'game_play') {
//			$order = 'jackpot_flag';
//		}

//		$tournament = $this->_getTable()
//						->addWhere('private_flag', $private_flag)
//						->addJoin($parent, 'parent_tournament_id', 'id', DatabaseQueryTableJoin::LEFT)
//						->addJoin($sport, 'tournament_sport_id', 'id', DatabaseQueryTableJoin::LEFT)
//						->addJoin($event_group, 'event_group_id', 'id', DatabaseQueryTableJoin::LEFT);
//
//		if(array_key_exists($order, $this->_member_list)) {
//			$tournament->addOrder($order, $dir);
//		}
//
//		$db =& $this->getDBO();
//
//		$query = new DatabaseQuery($tournament, $limit, $offset);
//		$db->setQuery($query->getSelect());

		$dir = ($direction == 'asc') ? 'ASC' : 'DESC';
		
		$db =& $this->getDBO();
		
		$query = '
			SELECT
				t.*,
				pt.name AS parent_name,
				s.name AS sport_name,
				eg.name AS event_group_name
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			LEFT JOIN
				' . $db->nameQuote('#__tournament') . ' AS pt
			ON
				t.parent_tournament_id = pt.id
		';
		
		$where = array();
		if (!empty($keyword)) {
			$orcond	= array();
			$orcond[] = 'LOWER(t.name) LIKE "%'. $keyword .'%"';
			$orcond[] = 'LOWER(pt.name) LIKE "%'. $keyword .'%"';
			if (ctype_digit($keyword)) {
				$orcond[] = 't.id = ' . $keyword;
			}
			$where[] = '(' . implode(' OR ', $orcond) . ')';
		}
		
		if (!is_null($private_flag)) {
			$where[] = 't.private_flag = ' . $db->quote($private_flag);
		}
		
		if ($start_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $start_date, $m))
		{
			$where[] = ' t.start_date >= ' . $db->quote($start_date . ' 00:00:00');
		}
		
		if ($end_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $end_date, $m))
		{
			$where[] = ' t.end_date <= ' . $db->quote($end_date . ' 23:59:59');
		}
		
		if (!empty($where)) {
			$query .= '
				WHERE
			' . implode(' AND ', $where);
		}
		
		$query .=' ORDER BY ' . $order . ' ' . $dir;

		$db->setQuery($query, $offset, $limit);
		return $db->loadObjectList();
	}

	/**
	 * Get the tournament event group for a tournament
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentAndEventGroupByTournamentID($id)
	{
		$tournament 	= $this->getTournament($id);
		$event_group_id = JModel::getInstance('TournamentEventGroup', 'TournamentModel')
							->getTournamentEventGroupByTournamentID($id)->id;

		$event_group 	= JModel::getInstance('EventGroup', 'TournamentModel')
							->getEventGroup($event_group_id);

		$value_list = array();
		foreach($tournament->getMemberList() as $name => $value) {
			$value_list[$name] = $tournament->$name;
		}

		foreach($event_group->getMemberList() as $name => $value) {
			$value_list[$name] = $event_group->$name;
		}

		return (object)$value_list;
	}

	/**
	 * Get a single tournament record.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentByName($name)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('name', $name)
		), 	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Get the ID of the current final jackpot meeting
	 *
	 * @deprecated This method still works but it's not really used/relevant
	 *
	 * @return integer
	 */
	public function getCurrentJackpotFinalID()
	{
		$table = new DatabaseQueryTable($this->_table_name);

		$table	->addColumn('id')
				->addWhere('jackpot_flag', 1)

				->addWhere('parent_tournament_id', 0,
					DatabaseQueryTableWhere::CONTEXT_AND,
					DatabaseQueryTableWhere::OPERATOR_LESS_THAN_OR_EQUAL)

				->addOrder('start_date', DatabaseQueryTableOrder::DESC)
				->addOrder('minimum_prize_pool', DatabaseQueryTableOrder::DESC);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table, 1);

		$db->setQuery($query->getSelect());
		return $db->loadResult();
	}

	/**
	 * Get a list of unpaid tournaments where the last event has taken place and been resulted.
	 *
	 * @return array
	 */
	public function getTournamentCompletedList()
	{
//		/*
//		 * Since this is probably a commonly used class I thought that I should use this method to explain
//		 * how to create a subquery with the Database library.
//		 *
//		 * Start by creating the main table and any joins. In this case the main table is tournament and
//		 * it joins event_group.
//		 */
//		$tournament 	= $this->_getTable();
//		$event_group 	= new DatabaseQueryTable('#__event_group');
//
//		/*
//		 * This is important. The reference column here is required in cases where a subquery will refer to
//		 * a column in the parent query. Here we see that the ID column from event_group will be needed by
//		 * the subquery. The final argument set to false tells the table class to return the column rather
//		 * than the default behaviour which is to return the caller object for method-chaining.
//		 */
//		$reference = $event_group->addColumn('id', false);
//		$tournament->addJoin($event_group, 'event_group_id', 'id');
//
//		/*
//		 * Here we start the process of creating the subquery table object. Nothing special.
//		 */
//		$event_event = new DatabaseQueryTable('#__event_group_event');
//
//		/*
//		 * The addWhere() method call uses the previously generated column object reference as its value so
//		 * that it can be tied back to the main query table.
//		 */
//		$event_event->addWhere('event_group_id', new DatabaseQueryTableValueColumn($reference));
//
//		$event = new DatabaseQueryTable('#__event');
//		$event	->addColumnFunction('*', 'count')
//				->addWhere('paid_flag', 0)
//				->addJoin($event_event, 'id', 'event_id');
//
//		/*
//		 * Now the where clause which checks the value of the subquery needs to be added to a table in the parent
//		 * query otherwise it won't be computed. This is done by adding the parent table object of the subquery
//		 * to a column as either the name or value. In this case that column is added as part of a where clause, but
//		 * that's not required.
//		 */
//		$sub = new DatabaseQueryTableColumnSubquery($event);
//		$event_group->addWhere($sub, new DatabaseQueryTableValue(0));
//
//		/*
//		 * Last special thing to do then is to add the subquery to the main table. This may seem strange given that it
//		 * has already been added as a column. The separate stack is used so that the alias space for the parent query
//		 * can be imported into the subquery when it comes time to build it, and so that methods related to columns
//		 * don't need to be modified to support referencing as it's only used in this case.
//		 */
//		$tournament	->addWhere('paid_flag', 1,
//						DatabaseQueryTableWhere::CONTEXT_AND,
//						DatabaseQueryTableWhere::OPERATOR_GREATER_OR_LESS_THAN)
//
//					->addWhere('start_date', new DatabaseQueryTableValueFunction(DatabaseQueryHelperFunction::NOW),
//						DatabaseQueryTableWhere::CONTEXT_AND,
//						DatabaseQueryTableWhere::OPERATOR_LESS_THAN)
//						;
//
//		/*
//		 * And lastly, pass parent table to a new query instance and get your query as normal.
//		 */
//		$query = new DatabaseQuery($tournament);
//		$db =& $this->getDBO();
//
//		$db->setQuery($query->getSelect());

		$db =& $this->getDBO();
		
		$query ='
			SELECT
				t.* ,
				p.tournament_prize_format_id AS prize_format_id,
				u.username AS owner
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			LEFT JOIN
				' . $db->nameQuote('#__tournament_private') . ' AS p
			ON
				t.id = p.tournament_id
			LEFT JOIN
				' . $db->nameQuote('#__users') . ' AS u
			ON
				u.id = p.user_id
			WHERE
				t.paid_flag <> 1
			AND
				t.start_date < NOW()
			AND
				(
					SELECT
						COUNT(*)
					FROM
						' . $db->nameQuote('#__event') . ' AS e
					INNER JOIN
						' . $db->nameQuote('#__event_group_event') . ' AS ege
					ON
						e.id = ege.event_id
					WHERE
						e.paid_flag = 0
					AND
						ege.event_group_id = eg.id
				) = 0
		';
		
		$db->setQuery($query);
		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Determine if the currently loaded tournament is in progress
	 *
	 * @return bool
	 */
	public function isInProgress()
	{
		if (empty($this->id)) {
			return false;
		}
		$model =& JModel::getInstance('TournamentTicket', 'TournamentModel');
		
		$count = $model->countTournamentEntrants($this->id);
		
		return (
			$this->start_date != self::UNDEFINED &&
			strtotime($this->start_date) < time() &&
			$count > 0
		);
	}


	/**
	 * Sports Tournament list
	 */
	public function getSportTournamentListByType($type = 0, $order = NULL, $direction = NULL, $limit = NULL, $offset = NULL)
	{
		if(is_null($order)) {
			$order = (empty($this->order)) ? 't.id' : $this->order;
		}

		if(is_null($direction)) {
			$direction = (empty($this->direction)) ? 'ASC' : $this->direction;
		}

		if(is_null($limit)) {
			$limit = (empty($this->limit)) ? 0 : $this->limit;
		}

		if(is_null($offset)) {
			$offset = (empty($this->offset)) ? 0 : $this->offset;
		}

		$db =& $this->getDBO();
		$query =
			'SELECT
				t.id,
				t.parent_tournament_id,
				t.event_group_id,
				t.name,
				t2.name AS parent_name,
				t.description,
				t.start_currency,
				t.start_date,
				t.end_date,
				t.jackpot_flag,
				t.buy_in,
				t.entry_fee,
				t.minimum_prize_pool,
				t.paid_flag,
				t.auto_create_flag,
				t.cancelled_flag,
				t.cancelled_reason,
				t.status_flag,
				t.private_flag,
				t.closed_betting_on_first_match_flag,
				t.betting_closed_date,
				t.reinvest_winnings_flag
				t.created_date,
				t.updated_date,
				eg.tournament_competition_id,
				eg.name AS event_group_name,
				c.name AS competition_name,
				s.name AS sport_name,
				s.description AS sport_description
			FROM
				' . $db->nameQuote( '#__tournament' ) . ' AS t
			LEFT JOIN
				' . $db->nameQuote( '#__tournament' ) . ' AS t2
			ON
				t.parent_tournament_id = t2.id
			INNER JOIN
				' . $db->nameQuote( '#__event_group' ) . ' AS eg
			ON
				eg.id = t.event_group_id
			INNER JOIN
				' . $db->nameQuote( '#__tournament_competition' ) . ' AS c
			ON
				c.id = eg.tournament_competition_id
			INNER JOIN
				' . $db->nameQuote( '#__tournament_sport' ) . ' AS s
			ON
				c.tournament_sport_id = s.id
			WHERE
				t.private_flag =' . $db->quote($type);


		if(!is_null($order)) {
			$query .= ' ORDER BY ' . $db->nameQuote($order);
		}

		if(!is_null($direction)) {
			$query .= ' ' . $direction;
		}

		$db->setQuery($query, $offset, $limit);
		return $db->loadObjectList();
	}
	
	/**
	 * Get tournament list by event group id
	 *
	 * @param integer $event_group_id
	 * @return array
	 */
	public function getTournamentListByEventGroupID($event_group_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('event_group_id', $event_group_id)
		), 	SuperModel::FINDER_LIST);
	}

	/**
	 * Return data needed to determine if unregistering is allowed for a user
	 *
	 * @param integer $tournament_id
	 * @param integer $user_id
	 * @return object
	 */
	public function unregisterAllowed($tournament_id, $user_id)
	{
		$tournament = $this->getTournament($tournament_id);

		$db =& $this->getDBO();
		$query =
			'SELECT
				t.start_date - NOW() AS time,
				COUNT(b.id) AS bet
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			ON
				t.id = tt.tournament_id
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet') . ' AS b
			ON
				tt.id = b.tournament_ticket_id
			LEFT JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS s
			ON
				s.id = b.bet_result_status_id
			WHERE
				t.id = ' . $db->quote($tournament_id) . '
			AND
				tt.user_id = ' . $db->quote($user_id) . '
			AND (s.name IS NULL OR s.name != ' . $db->quote('fully-refunded') . ')
			AND tt.refunded_flag = 0
			GROUP BY
				b.tournament_ticket_id';

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getTournamentActiveList($list_params = array())
	{
//		$date_criteria = SuperModel::getFinderCriteria(
//			'start_date',
//			new DatabaseQueryTableValueFunction(DatabaseQueryHelperFunction::NOW),
//			DatabaseQueryTableWhere::CONTEXT_AND,
//			DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL
//		);
//
//		return $this->find(array(
//			$date_criteria,
//			SuperModel::getFinderCriteria('paid_flag', 0),
//			SuperModel::getFinderCriteria('status_flag', 1)
//		), SuperModel::FINDER_LIST);

		$sport_id			= isset($list_params['sport_id']) ? $list_params['sport_id'] : null;
		$competition_id		= isset($list_params['competition_id']) ? $list_params['competition_id'] : null;
		$jackpot			= isset($list_params['jackpot']) ? $list_params['jackpot'] : false;
		$private			= isset($list_params['private']) ? $list_params['private'] : false;
		$limit				= isset($list_params['limit']) ? $list_params['limit'] : null;
		$type				= isset($list_params['type']) ? $list_params['type'] : null;
		$event_group_id		= isset($list_params['event_group_id']) ? $list_params['event_group_id'] : array();
		$order				= isset($list_params['order']) ? $list_params['order'] : null;
		
		$db =& $this->getDBO();
		$query = '
			SELECT
				t.id,
				t.tournament_sport_id,
				t.parent_tournament_id,
				t.event_group_id,
				t.name,
				t.description,
				t.start_currency,
				t.start_date,
				t.end_date,
				t.jackpot_flag,
				t.buy_in,
				t.entry_fee,
				t.minimum_prize_pool,
				t.free_credit_flag,
				t.tod_flag,
				t.paid_flag,
				t.auto_create_flag,
				t.cancelled_flag,
				t.cancelled_reason,
				t.status_flag,
				t.created_date,
				t.updated_date,
				t.private_flag,
				s.name AS sport_name,
				s.description AS sport_description,
				eg.id AS event_group_id,
				eg.name AS event_group_name,
				eg.meeting_code,
				eg.events,
				eg.track,
				eg.weather,
				c.name AS competition_name
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS c
			ON
				c.id = eg.tournament_competition_id
			WHERE
				t.end_date > NOW()
			AND
				t.status_flag = 1
			AND
				t.cancelled_flag = 0
		';
		
		if ($sport_id !== null) {
			$query .= ' AND t.tournament_sport_id = ' . $db->quote($sport_id);
		}
		
		if ($competition_id !== null) {
			$query .= ' AND c.id = ' . $db->quote($competition_id);
		}

		if ($jackpot !== false) {
			$query .= ' AND t.jackpot_flag = ' . $db->quote($jackpot);
		}

		if ($private !== false) {
			$query .= ' AND t.private_flag = ' . $db->quote($private);
		}
		
		switch ($type) {
			case 'sports':
				$query .= ' AND LOWER(s.name) NOT IN ("galloping", "harness", "greyhounds")';
				break;
			case 'racing':
				$query .= ' AND LOWER(s.name) IN ("galloping", "harness", "greyhounds")';
				break;
		}
		
		if (!empty($event_group_id)) {
			if (is_string($event_group_id)) {
				$event_group_id = array($event_group_id);
			}
			
			$clean_event_group_id = array();
			foreach ($event_group_id as $eg_id) {
				$clean_event_group_id[] = $db->quote($eg_id);
			}

			$query .= ' AND t.event_group_id IN (' . implode(', ', $clean_event_group_id) . ')';
		}

		if (empty($order)) {
			$query .= '
				ORDER BY
					t.start_date,
					eg.name,
					t.entry_fee';
		} else {
			$query .= '
				ORDER BY ' . $order;
		}
		
		if ($limit !== null) {
			$query .='
				LIMIT ' . (int)$limit;
		}

		$db->setQuery($query);
		return $db->loadObjectList();
	}


    public function getJackpotTournamentActiveList($list_params = array())
    {
//		$date_criteria = SuperModel::getFinderCriteria(
//			'start_date',
//			new DatabaseQueryTableValueFunction(DatabaseQueryHelperFunction::NOW),
//			DatabaseQueryTableWhere::CONTEXT_AND,
//			DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL
//		);
//
//		return $this->find(array(
//			$date_criteria,
//			SuperModel::getFinderCriteria('paid_flag', 0),
//			SuperModel::getFinderCriteria('status_flag', 1)
//		), SuperModel::FINDER_LIST);

        $sport_id			= isset($list_params['sport_id']) ? $list_params['sport_id'] : null;
        $competition_id		= isset($list_params['competition_id']) ? $list_params['competition_id'] : null;
        $jackpot			= isset($list_params['jackpot']) ? $list_params['jackpot'] : false;
        $private			= isset($list_params['private']) ? $list_params['private'] : false;
        $limit				= isset($list_params['limit']) ? $list_params['limit'] : null;
        $type				= isset($list_params['type']) ? $list_params['type'] : null;
        $event_group_id		= isset($list_params['event_group_id']) ? $list_params['event_group_id'] : array();
        $order				= isset($list_params['order']) ? $list_params['order'] : null;

        $db =& $this->getDBO();
        $query = '
			SELECT
				t.id,
				t.tournament_sport_id,
				t.parent_tournament_id,
				t.event_group_id,
				t.name,
				t.description,
				t.start_currency,
				t.start_date,
				t.end_date,
				t.jackpot_flag,
				t.buy_in,
				t.entry_fee,
				t.minimum_prize_pool,
				t.free_credit_flag,
				t.tod_flag,
				t.paid_flag,
				t.auto_create_flag,
				t.cancelled_flag,
				t.cancelled_reason,
				t.status_flag,
				t.created_date,
				t.updated_date,
				t.private_flag,
				s.name AS sport_name,
				s.description AS sport_description,
				eg.id AS event_group_id,
				eg.name AS event_group_name,
				eg.meeting_code,
				eg.events,
				eg.track,
				eg.weather,
				c.name AS competition_name
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS c
			ON
				c.id = eg.tournament_competition_id
			WHERE
				t.end_date > NOW()
			AND
				t.status_flag = 1
			AND
				t.cancelled_flag = 0
		';

        if ($sport_id !== null) {
            $query .= ' AND t.tournament_sport_id = ' . $db->quote($sport_id);
        }

        if ($competition_id !== null) {
            $query .= ' AND c.id = ' . $db->quote($competition_id);
        }

        if ($jackpot !== false) {
            $query .= ' AND t.jackpot_flag = ' . $db->quote($jackpot);
        }

        if ($private !== false) {
            $query .= ' AND t.private_flag = ' . $db->quote($private);
        }

        switch ($type) {
            case 'sports':
                $query .= ' AND LOWER(s.name) NOT IN ("galloping", "harness", "greyhounds")';
                break;
            case 'racing':
                $query .= ' AND LOWER(s.name) IN ("galloping", "harness", "greyhounds")';
                break;
        }

        if (!empty($event_group_id)) {
            if (is_string($event_group_id)) {
                $event_group_id = array($event_group_id);
            }

            $clean_event_group_id = array();
            foreach ($event_group_id as $eg_id) {
                $clean_event_group_id[] = $db->quote($eg_id);
            }

            $query .= ' AND t.event_group_id IN (' . implode(', ', $clean_event_group_id) . ')';
        }

        if (empty($order)) {
            $query .= '
				ORDER BY
					t.start_date,
					eg.name,
					t.entry_fee';
        } else {
            $query .= '
				ORDER BY ' . $order;
        }

        if ($limit !== null) {
            $query .='
				LIMIT ' . (int)$limit;
        }

        $db->setQuery($query);
        return $db->loadObjectList();
    }

	public function getTournamentJackpotParentList($id)
	{
		$sport 			= new DatabaseQueryTable('#__tournament_sport');
		$competition 	= new DatabaseQueryTable('#__tournament_competition');
		$event_group 	= new DatabaseQueryTable('#__event_group');
		$tournament 	= new DatabaseQueryTable('#__tournament');

		$competition->addJoin($sport, 'tournament_sport_id', 'id');
		$event_group->addJoin($competition, 'tournament_competition_id', 'id');

		$tournament	->addJoin($event_group, 'event_group_id', 'id')
					->addColumn('id')
					->addColumn('name')
					->addWhere('id', $id,
						DatabaseQueryTableWhere::CONTEXT_AND,
						DatabaseQueryTableWhere::OPERATOR_NOT_EQUAL);

		$query = new DatabaseQuery($tournament);

		$db =& $this->getDBO();
		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	/**
	 * Count the total number of sport tournaments.
	 *
	 * @return integer
	 */
	public function getTotalTournamentCount($params = array())
	{
//		$table = new DatabaseQueryTable('#__tournament');
//		$table->addColumn(new DatabaseQueryTableColumnFunction('*'));
//
//		if(!is_null($private_flag) && intval($private_flag) <= 1) {
//			$table->addWhere('private_flag', $private_flag);
//		}
//
//		$query = new DatabaseQuery($table);
//
//		$db =& $this->getDBO();
//		$db->setQuery($query->getSelect());

		$keyword		= isset($params['keyword']) ? $params['keyword'] : null;
		$private_flag	= isset($params['private_flag']) ? $params['private_flag'] : null;
		$start_date		= isset($params['start_date']) ? $params['start_date'] : null;
		$end_date		= isset($params['end_date']) ? $params['end_date'] : null;
		
		$db =& $this->getDBO();
		
		$query = '
			SELECT
				count(t.id)
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			LEFT JOIN
				' . $db->nameQuote('#__tournament') . ' AS pt
			ON
				t.parent_tournament_id = pt.id
		';
		
		$where = array();
		if (!empty($keyword)) {
			$orcond	= array();
			$orcond[] = 'LOWER(t.name) LIKE "%'. $keyword .'%"';
			$orcond[] = 'LOWER(pt.name) LIKE "%'. $keyword .'%"';
			if (ctype_digit($keyword)) {
				$orcond[] = 't.id = ' . $keyword;
			}
			$where[] = '(' . implode(' OR ', $orcond) . ')';
		}
		
		if (!is_null($private_flag)) {
			$where[] = 't.private_flag = ' . $db->quote($private_flag);
		}
		
		if ($start_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $start_date, $m))
		{
			$where[] = ' t.start_date >= ' . $db->quote($start_date . ' 00:00:00');
		}
		
		if ($end_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $end_date, $m))
		{
			$where[] = ' t.end_date <= ' . $db->quote($end_date . ' 23:59:59');
		}
		
		if (!empty($where)) {
			$query .= '
				WHERE
			' . implode(' AND ', $where);
		}

		$db->setQuery($query);

		return $db->loadResult();
	}
	
	/**get total number of Tournament from event ID
	 *
	 * @param $event_id
	 */
	public function getTotalTournamenCountByEventGroupID($event_group_id = null)
	{
		$db =& $this->getDBO();
		$query =
      		'SELECT
        		count(id)
		      FROM
		        ' . $db->nameQuote( '#__tournament' ) . '
		      WHERE
		        event_group_id = ' . $db->quote($event_group_id);
		$db->setQuery($query);
		return $db->loadResult();
	}
	

	/**
	 * Use the number of tickets purchased for a tournament to determine the current prize pool
	 * in cents.
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	public function calculateTournamentPrizePool($tournament_id)
	{
		$tournament = $this->getTournament($tournament_id);

		$db =& $this->getDBO();
		$query =
			'SELECT
				t.buy_in AS buy_in,
				COUNT(tt.user_id) AS entrants
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				tt.tournament_id = t.id
			WHERE
				tt.tournament_id = ' . $db->quote($tournament_id) . '
			AND
				tt.refunded_flag = 0
			GROUP BY
				tt.tournament_id';

		$db->setQuery($query);
		$data = $db->loadObject();

		$current_prize_pool = is_null($data) ? 0 : ($data->buy_in) * $data->entrants;
		return ($current_prize_pool > $tournament->minimum_prize_pool ? $current_prize_pool : $tournament->minimum_prize_pool);
	}

	/**
	 * Calculate the number of places paid for a tournament, and the payout if cash.
	 *
	 * @param object 	$tournament
	 * @param object 	$entrant_count
	 * @param int 		$prize_pool
	 * @return array
	 */
	public function calculateTournamentPlacesPaid($tournament, $entrant_count, $prize_pool)
	{
		$payout_model =& JModel::getInstance('TournamentPlacesPaid', 'TournamentModel');
		$final = $this->isFinished($tournament);

		if($final) {
			return $payout_model->getPrizeDistribution($tournament, $prize_pool);
		}

		return $payout_model->getPlaceList($tournament, $entrant_count, $prize_pool);
	}

	/**
	 * Publish or unpublish a tournament
	 *
	 * @param integer $id
	 * @param integer $status
	 * @return bool
	 */
	public function updateTournamentStatus($id, $status = 1)
	{
		$tournament = $this->load($id);
		$tournament->status_flag = $status;

		return $tournament->save();
	}

	/**
	 * Set the paid flag for a tournament
	 *
	 * @param integer $id
	 * @return bool
	 */
	public function setPaidFlagByTournamentID($id)
	{
		$tournament = $this->load($id);
		$tournament->paid_flag = 1;

		return $tournament->save();
	}

	/**
	 * Determine if a tournament has finished
	 *
	 * @param object $tournament
	 * @return bool
	 */
	public function isFinished($tournament)
	{
		return (!empty($tournament->cancelled_flag) || strtotime($tournament->end_date) < time());
	}

	/**
	 * Clone Tournament
	 */
	public function cloneTournament($id)
	{
		$tournament = $this->getTournament($id);

		$clone_tournament = new stdClass();
		
		foreach (array_keys($this->_member_list) as $field) {
			$clone_tournament->$field = $tournament->$field;
		}
		
		$clone_tournament->id				= null;
		$clone_tournament->name				= "CLONE OF: " . $clone_tournament->name;
		$clone_tournament->status_flag		= 0;
		$clone_tournament->paid_flag		= 0;
		$clone_tournament->auto_create_flag	= 0;
		$clone_tournament->cancelled_flag	= 0;
		$clone_tournament->cancelled_reason	= '';

		$clone_id = $this->store((array)$clone_tournament);

		return $clone_id;
	}

	/**
	 * Custom validation handler. There is a lot of business logic around tournaments so it should live in here.
	 *
	 * @return array
	 */
	public function validate()
	{
		parent::validate();

		if($this->isInProgress() && !defined('__DATA_MIGRATION_IN_PROGRESS__')) {
			$this->_validateInProgress();
		} else {
			$this->_validateDefault();
		}

		return $this->getErrorList();
	}

	/**
	 * Custom validation for when a tournament has already commenced.
	 *
	 * @return array
	 */
	private function _validateInProgress()
	{
		static $white_list = array(
			'name',
			'description'
		);

		$change_log = $this->getChangeLog();
		$diff = array_diff(array_keys($change_log), $white_list);
		
		if(!empty($diff)) {
			foreach($diff as $field) {
				$this->_addError(sprintf('Illegal value change prevented (%s)', $this->getDisplayName($field)), $field);
			}
		}
	}

	/**
	 * Custom validation for tournaments which have not yet commenced.
	 *
	 * @return array
	 */
	private function _validateDefault()
	{
		$id_list = array(
			'tournament_sport_id',
			'event_group_id'
		);

		foreach($id_list as $field) {
			if($this->$field <= 0) {
				$this->_addError(sprintf('Invalid value specified for %s', $this->getDisplayName($field)), $field);
			}
		}

		if(empty($this->minimum_prize_pool) &&
			(empty($this->buy_in) && empty($this->entry_fee)) &&
			!empty($this->jackpot_flag)) {
				$this->_addError('No minimum prize pool specified but free jackpot tournament selected', 'minimum_prize_pool');
		}

		if(!empty($this->parent_tournament_id) && $this->parent_tournament_id != -1) {
			$tournament = new TournamentModelTournament();
			$parent = $tournament->getTournament($this->parent_tournament_id);
			if($parent->entry_fee + $parent->buy_in <= 0) {
				$this->_addError('Parent tournament is free which will lead to infinite tickets', 'parent_tournament_id');
			}
		}
	}

	protected function _transformStartDate()
	{
		if(preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $this->start_date)) {
			return;
		}

		if(empty($this->event_group_id) || $this->event_group_id <= 0) {
			$this->_addError('Start date could not be calculated because no event group has been assigned', 'start_date');
			return;
		}
		
		//$event_group = JModel::getInstance('EventGroup', 'TournamentModel', $this->event_group_id);
		$event_group_model = JModel::getInstance('EventGroup', 'TournamentModel');
		$event_group = $event_group_model->getEventGroup($this->event_group_id);
		if(is_null($event_group)) {
			$this->_addError('Start date could not be calculated because the assigned event group was not found', 'start_date');
			return;
		}

		$this->start_date = $event_group->start_date;
		return true;
	}

	protected function _transformEndDate()
	{
		if(preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $this->end_date)) {
			return true;
		}

		if(empty($this->event_group_id) || $this->event_group_id <= 0) {
			$this->_addError('End date could not be calculated because no event group has been assigned', 'end_date');
			return;
		}

		$event_group = JModel::getInstance('EventGroup', 'TournamentModel', $this->event_group_id);
		if(is_null($event_group)) {
			$this->_addError('End date could not be calculated because the assigned event group was not found', 'end_date');
			return;
		}

		$event = JModel::getInstance('Event', 'TournamentModel')
					->getFinalEventByEventGroupID($event_group->id);

		if(is_null($event)) {
			$this->_addError('No events could be found for the specified event group', 'end_date');
			return;
		}

		$this->end_date = $event->start_date;
		return true;
	}

	private function _getBuyInByID($id)
	{
		static $cache = array();

		if(!array_key_exists($id, $cache)) {
			$cache[$id] = JModel::getInstance('TournamentBuyIn', 'TournamentModel')
							->getTournamentBuyIn($id);
		}

		return $cache[$id];
	}

	protected function _transformBuyIn()
	{
		if(!isset($this->ticket_value) && (string)$this->buy_in != SuperModel::UNDEFINED && preg_match('/[0-9]/', $this->buy_in)) {
			return;
		}
		
		$buy_in = $this->_getBuyInByID($this->ticket_value);
		if(is_null($buy_in)) {
			$this->_addError('Invalid buy-in formula specified', 'buy_in');
			return;
		}

		$this->buy_in = (int)($buy_in->buy_in * 100);
		return true;
	}

	protected function _transformEntryFee()
	{
		if(!isset($this->ticket_value) && (string)$this->entry_fee != SuperModel::UNDEFINED && preg_match('/[0-9]/', $this->entry_fee)) {
			return;
		}

		$buy_in = $this->_getBuyInByID($this->ticket_value);
		if(is_null($buy_in)) {
			$this->_addError('Invalid entry-fee formula specified', 'entry_fee');
			return;
		}

		$this->entry_fee = (int)($buy_in->entry_fee * 100);
		return true;
	}
	
	public function update_tournament($params)
	{
		$db =& $this->getDBO();

		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament') . '
			SET ';

		foreach($params as $field => $data){
			$query .= $field.' = '.$db->quote($data).', ';
		}

		$query .=
				'updated_date = NOW()
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
	
	public function getTournamentStartDateByMeetingID($meeting_id)
	{
		return $this->_getTournamentDatesByMeetingID($meeting_id, 'start_date');
	}
	
	
	public function getTournamentEndDateByMeetingID($meeting_id)
	{
		return $this->_getTournamentDatesByMeetingID($meeting_id, 'end_date');
	}
	
	
	public function getAppTournamentOfTheDay()
	{
		$db =& $this->getDBO();
		$query = '
			SELECT * FROM
				' . $db->nameQuote('#__tournament_of_day_venue') . ' 
			ORDER BY display_order';
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	private function _getTournamentDatesByMeetingID($meeting_id, $field)
	{
		$db =& $this->getDBO();
		
		$quoted_field = $db->nameQuote($field);
		
		$query = '
			SELECT
				' . $quoted_field . '
			FROM
				' . $db->nameQuote('#__tournament') . '
			WHERE
				event_group_id = ' . $db->quote($meeting_id) . ' 
			ORDER BY
				' . $quoted_field . ($field == 'end_date' ? ' ASC' : ' DESC') . '
			LIMIT 1
		';
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function groupUpdateStartDateByMeetingID($meeting_id, $start_date)
	{
		return $this->_groupUPdateDatesByMeetingID($meeting_id, $start_date, 'start_date');
	}
	
	public function groupUpdateEndDateByMeetingID($meeting_id, $end_date)
	{
		return $this->_groupUPdateDatesByMeetingID($meeting_id, $end_date, 'end_date');
	}
	
	private function _groupUPdateDatesByMeetingID($meeting_id, $start_date, $field)
	{
		$db =& $this->getDBO();
		
		$query = '
			UPDATE 
				' . $db->nameQuote('#__tournament') . '
			SET
				'. $db->nameQuote($field) . ' = ' . $db->quote($start_date) . ',
				updated_date = NOW()
			WHERE
				`event_group_id` = ' . $db->quote($meeting_id)
		;
		
		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Get the races and results for a tournament for Api getTournamentDetails
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getRaceListAndResultsByTournamentIdForApi($meeting_id, $number)
	{
		$db =& $this->getDBO();
		
		/*
		$query = "SELECT ".$db->nameQuote('id')." FROM " . $db->nameQuote('#__meeting') . " WHERE ".$db->nameQuote('meeting_code')." = " . $db->quote($code) ;
		$db->setQuery($query);
		
        $result = $db->loadRow();

        $query = "SELECT * FROM " . $db->nameQuote('#__race') . " WHERE ".$db->nameQuote('meeting_id')." = " . $db->quote($result[0]) ;
		*/
		
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
			AND
				ege.event_group_id = '. $db->quote($meeting_id) . '
			GROUP BY e.id
			';
		$db->setQuery($query);
		
        $races = $db->loadObjectList();
		
		$race_results = array();
		foreach($races as $race){
				 $race_results[$race->number]['race_id'] = $race->id;
				 $race_results[$race->number]['race_distance'] = $race->distance;
				 $race_results[$race->number]['togo'] = $this->formatCounterText(strtotime($race->start_date));
				 $race_results[$race->number]['weather'] = $race->weather;
				 $race_results[$race->number]['track'] = $race->track_condition;
				 //$query = "SELECT r.*, run.number, run.name, run.win_odds, run.place_odds FROM " . $db->nameQuote('#__result') . "AS r INNER JOIN " . $db->nameQuote('#__runner') . " AS run ON run.id = r.runner_id WHERE r.race_id = " . $db->quote($race->id) ;
				 $query = 'SELECT
							sr.id,
							sr.position,
							sr.win_dividend,
							sr.place_dividend,
							s.id AS selection_id,
							s.number AS runner_number,
							s.name AS selection_name,
							s.external_selection_id,
							sp.win_odds,
							sp.place_odds,
							sp.override_odds,
							mk.id AS market_id,
							mk.external_market_id,
							e.id AS event_id,
							e.external_event_id,
							e.event_status_id,
							e.paid_flag,
							e.trifecta_dividend,
							e.firstfour_dividend,
							e.quinella_dividend,
							e.exacta_dividend,
							e.trifecta_pool,
							e.firstfour_pool,
							e.quinella_pool,
							e.exacta_pool,
							mt.name AS market_name
						FROM
							' . $db->nameQuote('#__selection_result') . ' AS sr
						LEFT JOIN
							' . $db->nameQuote('#__selection') . ' AS s
							ON s.id = sr.selection_id
						LEFT JOIN
							' . $db->nameQuote('#__selection_price') . ' AS sp
							ON sp.selection_id = s.id
						INNER JOIN
							' . $db->nameQuote('#__market') . ' AS mk
							ON mk.id = s.market_id
						INNER JOIN
							' . $db->nameQuote('#__market_type') . ' AS mt
							ON mt.id = mk.market_type_id
						INNER JOIN
							' . $db->nameQuote('#__event') . ' AS e
							ON e.id = mk.event_id
						WHERE
							e.id = ' . $db->quote($race->id) . '
						ORDER BY
							sr.position
						';
		         $db->setQuery($query);
				 $winners = $db->loadObjectList();
				 foreach($winners as $winner){
                        $race_results[$race->number]['position'][$winner->position] = array('number' => $winner->runner_number, 'name' => $winner->selection_name, 'win_odds' => $winner->win_odds, 'place_odds' => $winner->place_odds); 
                      
				 }
                 
		}

		return $race_results;
	}

	/**
	 * Format the display of a countdown to a specified time
	 *
	 * @param integer $time
	 * @return string
	 */
	protected function formatCounterText($time) {
		if ($time < time()) {
			return FALSE;
		}

		$remaining = $time - time();

		$days = intval($remaining / 3600 / 24);
		$hours = intval(($remaining / 3600) % 24);
		$minutes = intval(($remaining / 60) % 60);
		$seconds = intval($remaining % 60);

		$text = $seconds . ' sec';
		if ($minutes > 0) {
			$text = $minutes . ' min';
		}

		if ($hours > 0) {
			$min_sec_text = '';

			if ($days == 0) {
				$min_sec_text = $text;
			}

			$text = $hours . ' hr ' . $min_sec_text;
		}

		if ($days > 0) {
			$text = $days . ' d ' . $text;
		}
		return $text;
	}	
	
	public function isThereTournamentOfTheDay ($startdate,$keyword='ALL',$id=0)
	{	
		$db =& $this->getDBO();
		$id = (!empty($id)) ? $id : 0;
		$query = "SELECT id,name FROM " . $db->nameQuote('#__tournament') . " 
				  WHERE start_date BETWEEN '".$db->getEscaped($startdate)." 00:00:00' AND '".$db->getEscaped($startdate)." 23:59:59' 
				  AND tod_flag='". strtoupper(trim($db->getEscaped($keyword))) ."' AND id<>" .$db->getEscaped($id);
		$db->setQuery($query);
		
        return $db->loadObjectList();
	}
}
