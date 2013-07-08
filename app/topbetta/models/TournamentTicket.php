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
				s.name AS sport_name,
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

		return $result[0]->current - $result[0]->unresulted;
	}
	
	/**
	 * Get a user's tournament list
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getUserTournamentList($user_id, $order = 't.id', $direction = 'ASC', $limit = null, $offset = null)
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

		$query =
			'SELECT
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
				t.parent_tournament_id,
				c.name AS competition_name
			FROM
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

		if(!is_null($order)) {
			$query .= ' ORDER BY ' . $order;
		}

		if(!is_null($direction)) {
			$query .= ' ' . $direction;
		}

		$result = \DB::select($query);

		return $result;
	}	

	public static function nextEventForEventGroup($eventGroupId) {
		
		$query = "SELECT e.*,eg.type_code AS type, eg.state, eg.name AS meeting_name, eg.id AS meeting_id 
		FROM tbdb_event_group_event AS ege 
		INNER JOIN tbdb_event AS e ON e.id = ege.event_id
		INNER JOIN tbdb_event_group AS eg ON ege.event_group_id = eg.id
		WHERE ege.event_group_id = '".$eventGroupId."'
		AND e.event_status_id = 1
		ORDER BY e.start_date ASC
		LIMIT 1";		

		$result = \DB::select($query);

		return $result;				
	}

}
