<?php
namespace TopBetta;

class TournamentTicket extends \Eloquent {
	protected $table = 'tbdb_tournament_ticket';

	protected $guarded = array();

	public static $rules = array();

	/**
	 * Count the number of entrants in a tournament using tournament tickets.
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	public static function countTournamentEntrants($tournamentId) {

		return TournamentTicket::where('tournament_id', '=', $tournamentId) -> where('refunded_flag', '=', 0) -> count();

	}

	public static function getTicketForUserId($userId, $tournamentId) {
		return TournamentTicket::where('tournament_id', '=', $tournamentId) -> where('user_id', '=', $userId) -> get();		
	}

	/**
	 * Get a user's tournament tickets
	 *
	 * @param integer $userId
	 * @return object
	 */
	public function getTournamentTicketActiveListByUserID($userId, $order = false, $includeRefunded = false)
	{

		$query =
			'SELECT
				tk.id,
				tk.tournament_id,
				tk.result_transaction_id,
				t.buy_in,
				t.entry_fee,
				t.start_currency,
				s.name AS sport_name,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.closed_betting_on_first_match_flag,
				t.reinvest_winnings_flag,
				t.tournament_sponsor_name,
				t.name AS tournament_name
			FROM
				tbdb_tournament_ticket AS tk
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				tbdb_tournament_sport AS s
			ON
				t.tournament_sport_id = s.id
			WHERE
				user_id = "' . $userId . '"
				AND t.paid_flag <> 1
				AND t.cancelled_flag = 0';

		if(!$includeRefunded) {
			$query .= ' AND tk.refunded_flag != 1';
		}

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY t.start_date ASC, tk.created_date DESC';
		}

		$result = \DB::select($query);

		return $result;
	}
	
	/**
	 * Get a user's tournament tickets
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getTournamentTicketRecentListByUserID($userId, $fromTime = false, $endTime = false, $paidFlag = null, $order = false, $includeRefunded = false)
	{
		
		$query =
			'SELECT
				tk.id,
				tk.tournament_id,
				tk.result_transaction_id,
				tk.winner_alert_flag,
				t.buy_in,
				t.entry_fee,
				t.start_currency,
				s.name AS sport_name,
				t.jackpot_flag,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.name AS tournament_name
			FROM
				tbdb_tournament_ticket AS tk
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				tbdb_tournament_sport AS s
			ON
				t.tournament_sport_id = s.id
			WHERE
				user_id = "' . $userId . '"';

		if($fromTime) {
			$query.= ' AND t.end_date > FROM_UNIXTIME(' . $fromTime . ')';
		}

		if($endTime) {
			$query.= ' AND t.end_date < FROM_UNIXTIME(' . $endTime . ')';
		}

		if(!is_null($paidFlag)) {
			$query.= ' AND t.paid_flag = ' . $paidFlag;
		}

		if(!$includeRefunded) {
			$query .= ' AND tk.refunded_flag != 1';
		}

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY t.start_date ASC, tk.created_date DESC';
		}

		//TODO: limit to 15 max or what they asked for
		
		$result = \DB::select($query);

		return $result;
	}	
	
	/**
	 * Get a single tournament ticket record by tournament and user ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public static function getTournamentTicketByUserAndTournamentID($userId, $tournamentId, $includeRefunded = false) {

		$query = 'SELECT
				id,
				tournament_id,
				user_id,
				entry_fee_transaction_id,
				buy_in_transaction_id,
				result_transaction_id,
				refunded_flag,
				resulted_flag,
				created_date,
				resulted_date
			FROM
				tbdb_tournament_ticket
			WHERE
				user_id = ' . $userId . '
			AND
				tournament_id = ' . $tournamentId;

		if (!$includeRefunded) {
			$query .= ' AND refunded_flag != 1';
		}

		$result = \DB::select($query);

		return $result;
	}

	/**
	 * Get a list of all entrants to a tournament
	 *
	 * @param integer $tournament_id
	 */
	public function getTournamentEntrantList($tournamentId) {

		$query = 'SELECT
				tt.user_id AS id,
				us.username,
				tu.city
			FROM
				tbdb_tournament_ticket AS tt
			INNER JOIN
				tbdb_users AS us
			ON
				tt.user_id = us.id
			LEFT JOIN
				tbdb_topbetta_user AS tu
			ON
				us.id = tu.user_id
			WHERE
				tt.tournament_id = ' . $tournamentId . '
			AND
				tt.refunded_flag != 1';

		$result = \DB::select($query);

		return $result;
		//return $db -> loadObjectList('user_id');
	}

	/**
	 * Calculate how much remaining currency a user has to spend by taking unresulted bets and subtracting from
	 * the current leaderboard.
	 *
	 * @param integer $tournament_id
	 * @param integer $user_id
	 * @return integer
	 */
	public function getAvailableTicketCurrency($tournamentId, $userId)
	{
		$ticket = $this->getTournamentTicketByUserAndTournamentID($userId, $tournamentId);
		if(is_null($ticket)) {
			return -1;
		}

		$query =
			'SELECT
				SUM(IF(b.resulted_flag=0,b.bet_amount,0)) AS unresulted,
				l.currency AS current
			FROM
				tbdb_tournament_ticket AS tt
			LEFT JOIN
				tbdb_tournament_bet AS b
			ON
				b.tournament_ticket_id = tt.id
			INNER JOIN
				tbdb_tournament_leaderboard AS l
			ON
				tt.tournament_id = l.tournament_id
			AND
				tt.user_id = l.user_id
			WHERE
				tt.tournament_id = "' . $tournamentId . '"
			AND
				tt.refunded_flag <> 1
			AND
				tt.user_id = "' . $userId . '"
			GROUP BY
				b.tournament_ticket_id';

		$result = \DB::select($query);
		var_dump($tournamentId, $userId, $ticket);
		die;
		return $result[0]->current - $result[0]->unresulted;
	}
	
	/**
	 * Get a user's tournament list
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getUserTournamentList($user_id, $order = 't.id', $direction = 'ASC', $limit = 25, $offset = null, $paid = null)
	{
		/*	
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
		*/

		$selectQuery = 'SELECT
				t.id,
				tk.result_transaction_id,
				tk.created_date,
				t.buy_in,
				t.entry_fee,
				s.name AS sport_name,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.paid_flag,
				t.start_currency,
				t.name AS tournament_name,
				t.jackpot_flag,
				t.tournament_sponsor_name,
				t.reinvest_winnings_flag,
				t.closed_betting_on_first_match_flag,
				t.parent_tournament_id,
				c.name AS competition_name';
				
			$selectCountQuery = "SELECT COUNT(*) AS total";	
				
			$query = ' FROM
				tbdb_tournament_ticket AS tk
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				tbdb_tournament_sport AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				eg.id = t.event_group_id
			INNER JOIN
				tbdb_tournament_competition AS c
				ON c.id = eg.tournament_competition_id
			WHERE
				user_id = "' . $user_id . '"
			AND
				tk.refunded_flag != 1
			AND
				t.cancelled_flag != 1';

        if ($paid) {
            $query .= ' AND t.paid_flag = 1 ';
        }
        $countQuery = $selectCountQuery . $query;

		if(!is_null($order)) {
			$query .= ' ORDER BY ' . $order;
		}

		if(!is_null($direction)) {
			$query .= ' ' . $direction;
		}
		
		if ($offset) {
			$query .= ' LIMIT ' . $offset . ',' . $limit;	
		} else {
			$query .= ' LIMIT ' . $limit;
		}


        // handle our normal query with results
		$fullQuery = $selectQuery . $query;
		
		$result = \DB::select($fullQuery);
		
		// handle our total count for this full query excluding page limits
		$numRows = \DB::select($countQuery);

		return array('result' => $result, 'num_rows' => $numRows[0]);	
	}

	/**
	 * This is a duplicate of the method above, but has the ability to accept a 'since' date. I opted to duplicated so I
	 * didnt have to alter the method
	 *
	 * @param $user_id
	 * @param string $order
	 * @param string $direction
	 * @param int $limit
	 * @param null $offset
	 * @param null $paid
	 * @return array
	 */
	public function getUserTournamentListSince($user_id, $order = 't.id', $direction = 'ASC', $limit = 25, $offset = null, $paid = null, $since)
	{
		$selectQuery = 'SELECT
				t.id,
				tk.result_transaction_id,
				tk.created_date,
				t.buy_in,
				t.entry_fee,
				s.name AS sport_name,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.paid_flag,
				t.start_currency,
				t.name AS tournament_name,
				t.jackpot_flag,
				t.tournament_sponsor_name,
				t.reinvest_winnings_flag,
				t.closed_betting_on_first_match_flag,
				t.parent_tournament_id,
				c.name AS competition_name';

		$selectCountQuery = "SELECT COUNT(*) AS total";

		$query = ' FROM
				tbdb_tournament_ticket AS tk
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				tbdb_tournament_sport AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				eg.id = t.event_group_id
			INNER JOIN
				tbdb_tournament_competition AS c
				ON c.id = eg.tournament_competition_id
			WHERE
				user_id = "' . $user_id . '"
			AND
				tk.refunded_flag != 1
			AND
				t.cancelled_flag != 1';

		if ($paid) {
			$query .= ' AND t.paid_flag = 1 ';
		}

		$query .= ' AND t.end_date >= "' . $since .'"' ;

		$countQuery = $selectCountQuery . $query;

		if(!is_null($order)) {
			$query .= ' ORDER BY ' . $order;
		}

		if(!is_null($direction)) {
			$query .= ' ' . $direction;
		}

		if ($offset) {
			$query .= ' LIMIT ' . $offset . ',' . $limit;
		} else {
			$query .= ' LIMIT ' . $limit;
		}


		// handle our normal query with results
		$fullQuery = $selectQuery . $query;

		$result = \DB::select($fullQuery);

		// handle our total count for this full query excluding page limits
		$numRows = \DB::select($countQuery);

		return array('result' => $result, 'num_rows' => $numRows[0]);
	}

	/**
	 * Return data needed to determine if unregistering is allowed for a user
	 *
	 * @param integer $tournamentId
	 * @param integer $ticketId
	 * @return object
	 */
	public function unregisterAllowed($tournamentId, $ticketId)
	{
		$allowed = true;
		$error = array();

		// HAS THE TOURNAMENT ALREADY STARTED
		$tournament = \TopBetta\Tournament::find($tournamentId);
		
		if ($tournament) {
			if (strtotime($tournament->start_date) < time()) {
				$error[] = \Lang::get('tournaments.already_started');
				$allowed = false;
			}	

			// HAVE THEY ALREADY PLACED ANY BETS
			$numBets = $this->getNumBetsForTicket($ticketId);

			if ($numBets > 0) {
				$error[] = \Lang::get('tournaments.already_bet');
				$allowed = false;
			}
		} else {
				$error[] = \Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId));
				$allowed = false;
		}

		return json_decode(json_encode(array("allowed" => $allowed, "error" => $error)), false);

	}	

	/**
	 * Refund a tournament ticket.
	 *
	 * @param integer $ticket_id
	 * @param boolean $full
	 * @return bool
	 */
	public function refundTicket($ticket, $full = false)
	{
		// $ticket       = $this->getTournamentTicket($ticket_id);
		$cost_method  = ($full) ? 'getTicketCost' : 'getTicketBuyIn';
		$cost         = $this->$cost_method($ticket->id);

		if(!empty($cost)) {
			$refundId = \TopBetta\FreeCreditBalance::_increment(\Auth::user()->id, $cost, 'refund');

			if (!$refundId) {
				return false;
			}

			$ticket->result_transaction_id = $refundId;
		}

		$ticket->refunded_flag = 1;

		return $ticket->save();
	}

	/**
	 * Calculate the cost of a ticket by adding the entry-fee and buy-in
	 *
	 * @param integer $ticket_id
	 * @return integer
	 */
	public function getTicketCost($ticketId)
	{
		$query = \DB::table('tbdb_tournament AS t')
			->join('tbdb_tournament_ticket AS tt', 'tt.tournament_id', '=', 't.id')
			->where('tt.id', $ticketId)
			->select(\DB::raw('t.entry_fee + t.buy_in AS cost'))
			->get();

		return (count($query) > 0) ? $query[0]->cost : false;	
	}	

	/**
	 * Get the buy-in cost of a purchased ticket.
	 *
	 * @param integer $ticket_id
	 * @return integer
	 */
	public function getTicketBuyIn($ticketId)
	{
		return \DB::table('tbdb_tournament AS t')
			->join('tbdb_tournament_ticket AS tt', 'tt.tournament_id', '=', 't.id')
			->where('tt.id', $ticketId)
			->pluck('t.buy_in');
	}	

	private function getNumBetsForTicket($ticketId) {
		return \DB::table('tbdb_tournament_ticket AS tt')
			->leftJoin('tbdb_tournament_bet AS b', 'tt.id', '=', 'b.tournament_ticket_id')
			->leftJoin('tbdb_bet_result_status AS s', 's.id', '=', 'b.bet_result_status_id')
			->where('tt.id', $ticketId)
			->whereRaw('(s.name IS NULL OR s.name != "fully-refunded")')
			->where('tt.refunded_flag',0)
			->groupBy('b.tournament_ticket_id')
			->count('b.id');		
	}

	public static function nextEventForEventGroup($eventGroupId) {
		
		$query = "SELECT e.*,eg.type_code AS type, eg.state, eg.name AS meeting_name, eg.id AS meeting_id, ts.name AS sport_name 
		FROM tbdb_event_group_event AS ege 
		INNER JOIN tbdb_event AS e ON e.id = ege.event_id
		INNER JOIN tbdb_event_group AS eg ON ege.event_group_id = eg.id
		LEFT JOIN tbdb_tournament_sport AS ts ON ts.id = eg.sport_id
		WHERE ege.event_group_id = '".$eventGroupId."'
		AND e.event_status_id = 1
		ORDER BY e.start_date ASC
		LIMIT 1";		

		$result = \DB::select($query);

		return $result;				
	}

	public function tournament() {
		return $this->belongsTo('\TopBetta\Tournament', 'tournament_id');
	}

}
