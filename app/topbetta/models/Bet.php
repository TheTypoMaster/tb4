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
		
		$query = "SELECT b.id, b.bet_freebet_flag AS freebet, bt.id AS bet_type, rs.name AS result_status,
	      		e.id AS event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.id AS selection_id,
	      		s.name AS selection_name,
	      		s.number AS selection_number,
	      		bat.amount AS bet_total
			FROM
				tbdb_bet AS b
			INNER JOIN
				tbdb_bet_type AS bt
			ON
				bt.id = b.bet_type_id
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
			SELECT b.id, b.bet_freebet_flag AS freebet, bt.id AS bet_type, rs.name AS result_status,
	      		e.id AS event_id,
	      		e.external_event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.name AS selection_name,
	      		s.number AS selection_number,
	      		s.id AS selection_id,
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
		
		$result = \DB::select($query);

		return $result;			
	}	

}
