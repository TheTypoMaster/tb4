<?php
namespace TopBetta;

class Bet extends \Eloquent {

	protected $table = 'tbdb_bet';
	protected $guarded = array();

	public static $rules = array();

	/**
	 * Get bet transaction details.
	 * @param $transactionID
	 * @return int
	 * - The details of a bet transaction
	 */
	static public function getBetDetails($transactionID) {
		return Bet::where('invoice_id', '=', $transactionID) -> get();
	}

	/**
	 * Check if bet exists based on IGAS
	 * @param $transactionID
	 * @return int
	 * - ID of the bet transaction
	 */
	static public function getBetExists($transactionID) {
		return Bet::where('invoice_id', '=', $transactionID) -> pluck('id');
	}

	/**
	 * Get the data required to place a legacy bet from just the selection id
	 *
	 * @param $selectionId int
	 * @return array
	 */
	public function getLegacyBetData($selectionId) {
		return \DB::table('tbdb_selection AS s') 
		-> join('tbdb_market AS m', 's.market_id', '=', 'm.id') 
		-> join('tbdb_event_group_event AS e', 'm.event_id', '=', 'e.event_id')  
		-> where('s.id', '=', $selectionId) 
		-> select('s.market_id', 's.wager_id', 's.number', 'm.event_id AS race_id', 'e.event_group_id AS meeting_id') -> get();

	}
	
	/**
	 * Get active live bet records by user id
	 *
	 * @param integer $userId
	 * @return array
	 */	
	public function getActiveLiveBetsForUserId($userId) {
		
		$query = "SELECT b.id, bo.keyword AS origin, b.bet_freebet_flag AS freebet, bt.id AS bet_type, rs.name AS result_status,
	      		e.id AS event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.id AS selection_id,
	      		s.name AS selection_name,
	      		s.number AS selection_number,
	      		bat.amount AS bet_total, b.created_date, b.invoice_id, b.bet_transaction_id
			FROM
				tbdb_bet AS b
			INNER JOIN
				tbdb_bet_type AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				tbdb_bet_origin AS bo
			ON
				b.bet_origin_id = bo.id				
			INNER JOIN
				tbdb_bet_result_status AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				tbdb_bet_selection AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				tbdb_selection AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				tbdb_market AS m
			ON
				m.id = s.market_id
			INNER JOIN
				tbdb_event AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				tbdb_account_transaction AS bat
			ON
				bat.id = b.bet_transaction_id
			WHERE
				b.user_id = '$userId'
			AND
				b.resulted_flag = 0
			
			GROUP BY
				b.id";
				
		$result = \DB::select($query);

		return $result;						
		
	}
	
	/**
	 * Get recent live bet records by user id
	 *
	 * @param integer $user_id
	 * @return array
	 */
	public function getRecentLiveBetsForUserId($userId, $fromTime = false, $endTime = false, $resultedFlag = null, $order = false)
	{

		$query = '
			SELECT b.id, bo.keyword AS origin, b.bet_freebet_flag AS freebet, bt.id AS bet_type, rs.name AS result_status,
	      		e.id AS event_id,
	      		e.external_event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.name AS selection_name,
	      		s.number AS selection_number,
	      		s.id AS selection_id,
	      		bat.amount AS bet_total,
	      		rat.amount AS win_amount,
	      		fat.amount AS refund_amount, b.created_date, b.invoice_id, b.bet_transaction_id
			FROM
				tbdb_bet AS b
			INNER JOIN
				tbdb_bet_type AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				tbdb_bet_origin AS bo
			ON
				b.bet_origin_id = bo.id						
			INNER JOIN
				tbdb_bet_result_status AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				tbdb_bet_selection AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				tbdb_selection AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				tbdb_market AS m
			ON
				m.id = s.market_id
			INNER JOIN
				tbdb_event AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				tbdb_account_transaction AS bat
			ON
				bat.id = b.bet_transaction_id
			LEFT JOIN
				tbdb_account_transaction AS rat
			ON
				rat.id = b.result_transaction_id
			LEFT JOIN
				tbdb_account_transaction AS fat
			ON
				fat.id = b.refund_transaction_id
			WHERE
				b.user_id = "' . $userId . '"';
		
		if ($fromTime) {
			$query .= ' AND e.start_date >= FROM_UNIXTIME(' . $fromTime . ')';
		}
		
		if ($endTime) {
			$query .= ' AND e.start_date <= FROM_UNIXTIME(' . $endTime . ')';
		}
		
		if(!is_null($resultedFlag)) {
			$query.= ' AND b.resulted_flag = ' . $resultedFlag;
		}
		
		$query .= '
			GROUP BY
				b.id
			';

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY e.start_date ASC, b.created_date DESC';
		}
		
		//TODO: limit to 15 max or what they asked for
		
		$result = \DB::select($query);

		return $result;			
	}	

	/**
	 * Get fliltered bet records
	 *
	 * @param array $filter
	 * @return array
	 */
	public function getBetFilterList($filter=array(), $order=null, $direction='ASC', $limit=null, $offset=null)
	{
		$keyword		= isset($filter['keyword']) ? $filter['keyword'] : null;
		$result_type	= isset($filter['result_type']) ? $filter['result_type'] : null;
		$from_time		= isset($filter['from_time']) ? $filter['from_time'] : null;
		$to_time		= isset($filter['to_time']) ? $filter['to_time'] : null;
		$from_amount	= isset($filter['from_amount']) ? $filter['from_amount'] : null;
		$to_amount		= isset($filter['to_amount']) ? $filter['to_amount'] : null;
		$user_id		= isset($filter['user_id']) ? $filter['user_id'] : null;
		
		if (is_null($order)) {
			$order = 'b.id';
		}
		
		$query = '
			SELECT
	      		b.*,
	      		u.username,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		e.id AS event_id,
	      		e.external_event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		e.trifecta_dividend,
	      		e.firstfour_dividend,
	      		e.quinella_dividend,
	      		e.exacta_dividend,
	      		s.name AS selection_name,
	      		bs.selection_id,
	      		s.external_selection_id,
	      		bat.amount AS bet_total,
	      		rat.amount AS win_amount,
	      		fat.amount AS refund_amount
			FROM
				tbdb_bet AS b
			INNER JOIN
				tbdb_bet_type AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				tbdb_bet_result_status AS rs
			ON
				rs.id = b.bet_result_status_id
			INNER JOIN
				tbdb_users AS u
			ON
				u.id = b.user_id
			INNER JOIN
				tbdb_bet_selection AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				tbdb_selection AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				tbdb_market AS m
			ON
				m.id = s.market_id
			INNER JOIN
				tbdb_event AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				tbdb_account_transaction AS bat
			ON
				bat.id = b.bet_transaction_id
			LEFT JOIN
				tbdb_account_transaction AS rat
			ON
				rat.id = b.result_transaction_id
			LEFT JOIN
				tbdb_account_transaction AS fat
			ON
				fat.id = b.refund_transaction_id
			';
		$where = array();
		
		if ($keyword) {
			$or_cond	= array();
			$key_word	= $keyword;
			$or_cond[]	= 'u.username LIKE "%' . $keyword . '%"';
			$or_cond[]	= 'e.name LIKE "%' . $keyword . '%"';
			$or_cond[]	= 's.name LIKE "%' . $keyword . '%"';
			
			if (ctype_digit($keyword)) {
				$or_cond[]	= 'b.id = ' . $keyword;
				$or_cond[]	= 'b.external_bet_id = ' . $keyword;
			}
			
			$where[] = '(' . implode(' OR ', $or_cond) . ')';
		}
		
		if ($user_id) {
			$where[] = 'b.user_id = ' . $user_id;
		}
		
		switch ($result_type) {
			case 'unresulted':
				$where[] = 'b.resulted_flag = 0';
				$where[] = 'b.refunded_flag = 0';
				break;
			case 'winning':
				$where[] = 'b.result_transaction_id IS NOT NULL';
				$where[] = 'b.result_transaction_id > 0';
				$where[] = 'b.resulted_flag = 1';
				$where[] = 'rat.amount > 0';
				break;
			case 'losing':
				$where[] = '(b.result_transaction_id IS NULL OR b.result_transaction_id=0 OR rat.amount = 0)';
				$where[] = 'b.resulted_flag = 1';
				$where[] = 'b.refund_transaction_id IS NULL';
				break;
			case 'refunded':
				$where[] = 'b.refunded_flag = 1';
				$where[] = '(b.result_transaction_id IS NULL OR b.result_transaction_id = 0 OR rat.amount = 0)';
				break;
		}
		
		if ($from_time) {
			$where[] = 'b.created_date >= FROM_UNIXTIME(' . $from_time . ')';
		}
		
		if ($to_time) {
			$where[] = 'b.created_date < FROM_UNIXTIME(' . $to_time . ')';
		}
		
		if (ctype_digit($from_amount)) {
			$where[] = 'abs(bat.amount) >=' . $from_amount * 100;
		}
		
		if (ctype_digit($to_amount)) {
			$where[] = 'abs(bat.amount) <=' . $to_amount * 100;
		}
		
		if (count($where) > 0) {
			$query .='
				WHERE
			';
			$query .= implode(' AND ', $where);
		}
		
		$query .= '
			GROUP BY
				b.id
			';

		$query .= ' ORDER BY ' . $order;
				
		if ($offset) {
			$query .= ' LIMIT ' . $offset . ',' . $limit;	
		} else {
			$query .= ' LIMIT ' . $limit;
		}

		$result = \DB::select($query);

		return $result;	
	}

	public function getTournamentBetListByEventIDAndTicketID($event_id, $ticket_id)
	{

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
				tbdb_tournament_bet AS b
			INNER JOIN
				tbdb_tournament_ticket AS ticket
			ON
				b.tournament_ticket_id = ticket.id
			INNER JOIN
				tbdb_bet_result_status AS s
			ON
				b.bet_result_status_id = s.id
			INNER JOIN
				tbdb_bet_type AS t
			ON
				b.bet_type_id = t.id
			INNER JOIN
				tbdb_tournament_bet_selection AS ts
			ON
				ts.tournament_bet_id = b.id
			INNER JOIN
				tbdb_selection AS selection
			ON
				ts.selection_id = selection.id
			LEFT JOIN
				tbdb_selection_price AS sp
			ON
				sp.selection_id = selection.id
			LEFT JOIN
				tbdb_selection_result AS sr
			ON
				sr.selection_id = selection.id
			INNER JOIN
				tbdb_market AS m
			ON
				selection.market_id = m.id
			INNER JOIN
				tbdb_event AS e
			ON
				m.event_id = e.id
			WHERE
				e.id = "' . $event_id . '"
			AND
				ticket.id = "' . $ticket_id . '"
			ORDER BY
				b.id ASC';

		$result = \DB::select($query);

		return $result;	
	}

}
