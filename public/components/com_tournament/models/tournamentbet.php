<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelTournamentBet extends SuperModel
{
	protected $_table_name = '#__tournament_bet';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'tournament_ticket_id' => array(
			'name' => 'Tournament Ticket ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_result_status_id' => array(
			'name' => 'Bet Result Status ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_type_id' => array(
			'name' => 'Bet Type ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_product_id' => array(
			'name' => 'Bet Product ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_amount' => array(
			'name' => 'Bet Amount',
			'type' => self::TYPE_INTEGER
		),
		'win_amount' => array(
			'name' => 'Win Amount',
			'type' => self::TYPE_INTEGER
		),
		'fixed_odds' => array(
			'name' => 'Fixed Odds',
			'type' => self::TYPE_FLOAT
		),
		'flexi_flag' => array(
			'name' => 'Flexi-flag',
			'type' => self::TYPE_INTEGER
		),
		'resulted_flag' => array(
			'name' => 'Resulted Flag',
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

	public function getTournamentBet($id)
	{
		return $this->load($id);
	}

	public function getTournamentBetListByEventIDAndTicketID($event_id, $ticket_id)
	{
//		$event = new DatabaseQueryTable('#__event');
//		$event->addWhere('id', $event_id);
//
//		$market = new DatabaseQueryTable('#__market');
//		$market->addJoin($event, 'event_id', 'id');
//
//		$selection = new DatabaseQueryTable('#__selection');
//		$selection->addJoin($market, 'market_id', 'id');
//
//		$bet_selection = new DatabaseQueryTable('#__tournament_bet_selection');
//		$bet_selection->addJoin($selection, 'selection_id', 'id');
//
//		$bet = new DatabaseQueryTable($this->_table_name);
//		$bet->addWhere('tournament_ticket_id', $ticket_id)
//			->addJoin($bet_selection, 'id', 'tournament_bet_id');
//
//		$db =& $this->getDBO();
//		$query = new DatabaseQuery($bet);
//
//		$db->setQuery($query->getSelect());
//		return $this->_loadModelList($db->loadObjectList());

		
		$db =& $this->getDBO();
		$query =
			'SELECT
				b.id,
				b.tournament_ticket_id,
				b.bet_amount,
				b.win_amount,
				b.fixed_odds,
				b.flexi_flag,
				b.resulted_flag,
				s.name AS bet_status,
				t.name AS bet_type,
				selection.number AS runner_number,
				selection.name AS selection_name,
				sp.win_odds,
				sp.place_odds,
				sp.bet_product_id,
				sr.win_dividend,
				sr.place_dividend
			FROM
				' . $db->nameQuote('#__tournament_bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__tournament_ticket') . ' AS ticket
			ON
				b.tournament_ticket_id = ticket.id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS s
			ON
				b.bet_result_status_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS t
			ON
				b.bet_type_id = t.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_bet_selection') . ' AS ts
			ON
				ts.tournament_bet_id = b.id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS selection
			ON
				ts.selection_id = selection.id
			LEFT JOIN
				' . $db->nameQuote('#__selection_price') . ' AS sp
			ON
				sp.selection_id = selection.id
			LEFT JOIN
				' . $db->nameQuote('#__selection_result') . ' AS sr
			ON
				sr.selection_id = selection.id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				selection.market_id = m.id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				m.event_id = e.id
			WHERE
				e.id = ' . $db->quote($event_id) . '
			AND
				ticket.id = ' . $db->quote($ticket_id) . '
			ORDER BY
				b.id ASC';

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getUnresultedTournamentBetListByEventID($event_id)
	{
		//first join trail
		$sport = new DatabaseQueryTable('#__tournament_sport');
		$sport->addColumn('racing_flag');
		
		$competition = new DatabaseQueryTable('#__tournament_competition');
		$competition->addJoin($sport, 'tournament_sport_id', 'id');
		
		$event_group = new DatabaseQueryTable('#__event_group');
		$event_group->addJoin($competition, 'tournament_competition_id', 'id');
		
		$event_group_event = new DatabaseQueryTable('#__event_group_event');
		$event_group_event->addJoin($event_group, 'event_group_id' , 'id');
		
		// where 
		$event = new DatabaseQueryTable('#__event');
		$event->addWhere('id', $event_id);
		$event->addJoin($event_group_event, 'id', 'event_id')
		->addColumn(new DatabaseQueryTableColumn('name','event_name'));
		
		// second join trail
		$market = new DatabaseQueryTable('#__market');
		$market->addColumn('refund_flag');
		$market->addJoin($event, 'event_id', 'id');
		
		$selection_status = new DatabaseQueryTable('#__selection_status');
		$selection_status->addColumn(new DatabaseQueryTableColumn('keyword','selection_status'));
		
		$selection = new DatabaseQueryTable('#__selection');
		$selection->addJoin($market, 'market_id', 'id')
		->addJoin($selection_status, 'selection_status_id', 'id')
		->addColumn(new DatabaseQueryTableColumn('name','selection_name'));
		
		$selection_result = new DatabaseQueryTable('#__selection_result');
		$selection_result->addColumn('win_dividend')
			->addColumn('place_dividend')
			->addColumn('position')
			->addColumn(new DatabaseQueryTableColumn('id','record_exists'));

		$bet_selection = new DatabaseQueryTable('#__tournament_bet_selection');
		$bet_selection->addJoin($selection, 'selection_id', 'id')
			->addJoin($selection_result, 'selection_id', 'selection_id', DatabaseQueryTableJoin::LEFT)
			->addColumn('selection_id');
	
		$bet_type = new DatabaseQueryTable('#__bet_type');
		$bet_type->addColumn(new DatabaseQueryTableColumn('name','bet_type'));
		
		$bet = $this->_getTable();
		$bet->addWhere('resulted_flag', 1,
						DatabaseQueryTableWhere::CONTEXT_AND,
						DatabaseQueryTableWhere::OPERATOR_GREATER_OR_LESS_THAN)
			->addJoin($bet_selection, 'id', 'tournament_bet_id')
			->addJoin($bet_type, 'bet_type_id', 'id');

		$db =& $this->getDBO();
		$query = new DatabaseQuery($bet);
		$db->setQuery($query->getSelect());
		return $this->_loadModelList($db->loadObjectList());
	}
	
	/**
	 * To check has the betting started or not
	 */
	function isBettingStartedByTournamentId($tournament_id)
	{
		if($tournament_id){
			$db =& $this->getDBO();
			$query =
				'SELECT
					COUNT(b.id) AS total_bets
				FROM
					' . $db->nameQuote('#__tournament_sport_bet') . ' AS b
				INNER JOIN
					' . $db->nameQuote('#__tournament_ticket') . ' AS t
				ON
					b.tournament_ticket_id = t.id
				WHERE
					t.tournament_id = ' . $db->quote($tournament_id);
			$db->setQuery($query);
			$count = $db->loadObject();
		}
		if((int)$count->total_bets > 0) return true;
		else return false;
	}
	/**
	 * To check has the betting started for an Event group or not
	 */
	function isBettingStartedByEventGroupId($event_group_id)
	{
		if ($event_group_id) {
			$db =& $this->getDBO();
			$query = '
				SELECT
					COUNT(b.id) AS total_bets
				FROM
					' . $db->nameQuote('#__tournament_bet') . ' AS b
				INNER JOIN
					' . $db->nameQuote('#__tournament_ticket') . ' AS tt
				ON
					b.tournament_ticket_id = tt.id
				INNER JOIN
					' . $db->nameQuote('#__tournament') . ' AS t
				ON
					tt.tournament_id = t.id
				WHERE
					t.event_group_id = ' . $db->quote($event_group_id);
			$db->setQuery($query);
			$count = $db->loadObject();
		}
		return ((int)$count->total_bets > 0);
	}
	/**
	 * To check has the betting started for a competition or not
	 */
	function isBettingStartedByCompetitionId($competition_id)
	{
		if ($competition_id) {
			$db =& $this->getDBO();
			$query = '
				SELECT
					COUNT(b.id) AS total_bets
				FROM
					' . $db->nameQuote('#__tournament_bet') . ' AS b
				INNER JOIN
					' . $db->nameQuote('#__tournament_ticket') . ' AS tt
				ON
					b.tournament_ticket_id = tt.id
				INNER JOIN
					' . $db->nameQuote('#__tournament') . ' AS t
				ON
					tt.tournament_id = t.id
				INNER JOIN
					' . $db->nameQuote('#__event_group') . ' AS eg
				ON
					eg.id = t.event_group_id
				WHERE
					eg.tournament_competition_id = ' . $db->quote($competition_id);
			$db->setQuery($query);
			$count = $db->loadObject();
		}
		return ((int)$count->total_bets > 0);
	}

	public function userHasBet($user_id, $tournament_id, $event_id)
	{
//		$event = new DatabaseQueryTable('#__event');
//		$event->addWhere('id', $event_id);
//
//		$market = new DatabaseQueryTable('#__market');
//		$market->addJoin($event, 'event_id', 'id');
//
//		$selection = new DatabaseQueryTable('#__selection');
//		$selection->addJoin($market, 'market_id', 'id');
//
//		$bet_selection = new DatabaseQueryTable('#__tournament_bet_selection');
//		$bet_selection->addJoin($selection, 'selection_id', 'id');
//
//		$ticket = new DatabaseQueryTable('#__tournament_ticket');
//		$ticket->addWhere('user_id', $user_id);
//
//		$table = new DatabaseQueryTable($this->_table_name);
//		$table	->addFunction('*')
//				->addJoin($ticket, 'tournament_ticket_id', 'id')
//				->addJoin($bet_selection, 'id', 'tournament_bet_id');
//
//		$db =& $this->getDBO();
//		$query = new DatabaseQuery($table);
//
//		$db->setQuery($query->getSelect());

		$db =& $this->getDBO();
		$query =
			'SELECT
				count(*) AS count
			FROM
				' . $db->nameQuote('#__tournament_bet') . ' AS tb
			INNER JOIN
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			ON
				tt.id = tb.tournament_ticket_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_bet_selection') . ' AS bs
			ON
				bs.tournament_bet_id = tb.id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON
				bs.selection_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			WHERE
				m.event_id = ' . $db->quote($event_id) . '
			AND
				tt.user_id = ' . $db->quote($user_id) . '
			AND	
				tt.tournament_id = ' . $db->quote($tournament_id) 
			;

		$db->setQuery($query);
		$result = $db->loadResult();
		return ($result > 0);
	}
	
	/**
	 * Get Unresulted bets by match id
	 * @param $match_id
	 * @return array
	 */
	public function getUnresultedTournamentSportBetListByMatchID($match_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				b.id,
				b.tournament_ticket_id,
				b.tournament_offer_id,
				b.bet_result_status_id,
				b.bet_amount,
				b.win_amount,
				b.odds,
				b.resulted_flag,
				b.created_date,
				b.updated_date,
				mk.id AS tournament_market_id,
				mk.tournament_match_id,
				mk.refund_flag,
				o.name as offer_name,
				o.id AS tournament_offer_id,
				bt.name as market_name
			FROM
				' . $db->nameQuote('#__tournament_sport_bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__tournament_offer') . ' AS o
			ON
				b.tournament_offer_id = o.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_market') . ' AS mk
			ON
				mk.id = o.tournament_market_id
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = mk.bet_type_id
			WHERE
				mk.tournament_match_id = ' . $db->quote($match_id) . '
			AND
				b.resulted_flag = 0
			ORDER BY
				b.id ASC';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTournamentBetTotalsByEventIDAndTicketID($event_id, $ticket_id)
	{
		$db =& $this->getDBO();
		$query ='
			SELECT
				sum(b.bet_amount) AS bet_total
			FROM
				' . $db->nameQuote('#__tournament_bet') . ' AS b
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet_selection') . ' AS bs
			ON
				bs.tournament_bet_id = b.id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			WHERE
				b.tournament_ticket_id = ' . $db->quote($ticket_id) . '
			AND
				m.event_id = ' . $db->quote($event_id) 
			;
		
		$db->setQuery($query);
		return $db->loadResult();
	}


	public function getTournamentBetTotalsByMarketIDAndTicketID($market_id, $ticket_id)
	{
		$db =& $this->getDBO();
		$query ='
			SELECT
				sum(b.bet_amount) AS bet_total
			FROM
				' . $db->nameQuote('#__tournament_bet') . ' AS b
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet_selection') . ' AS bs
			ON
				bs.tournament_bet_id = b.id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON
				s.id = bs.selection_id
			WHERE
				b.tournament_ticket_id = ' . $db->quote($ticket_id) . '
			AND
				s.market_id = ' . $db->quote($market_id) . '
			GROUP BY
				b.id
			';
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function getTournamentBetTotalsBySelectionIDAndTicketID($selection_id, $ticket_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				sum(bet_amount) AS bet_total
			FROM
				' . $db->nameQuote('#__tournament_bet') . ' b
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet_selection') . ' bs
			ON
				bs.tournament_bet_id = b.id
			WHERE
				selection_id = ' . $db->quote($selection_id) . '
			AND
				tournament_ticket_id = ' . $db->quote($ticket_id) . '
			GROUP BY
				b.id	
			';
		$db->setQuery($query);

		return $db->loadResult();
	}

	public function storeUsingTypeNames($params)
	{
		$bt_model =& JModel::getInstance('BetType', 'BettingModel');
		$bs_model =& JModel::getInstance('BetResultStatus', 'BettingModel');

		$params['bet_type_id'] 			= $bt_model->getBetTypeByName($params['bet_type'])->id;
		$params['bet_result_status_id'] = $bs_model->getBetResultStatusByName($params['bet_result_status'])->id;

		return $this->store($params);
	}
}
