<?php namespace TopBetta;

class RaceSelection extends \Eloquent {

	protected $table = 'tbdb_selection';
	
	/**
	 * Check if a selection exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionExists($meetingId, $raceNo, $runnerNo){
	
	
		//TODO: can this be done outsideuery builder
		return \DB::table('tbdb_selection')
					->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
					->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
					->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
					->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
					->where('tbdb_event_group.external_event_group_id',$meetingId )
					->where('tbdb_event.number', $raceNo)
					->where('tbdb_selection.number', $runnerNo)->pluck('tbdb_selection.id');
	
		//return $this::where('number', $raceNo)->where('external_event_group_id', '=', $meetingId)->racemeetings;
		//return self::racemeetings;
	}
	
	//TODO: this query is straight from joomla. Rebuild in Eloquent
	static public function getRunnersForRaceId ($raceId) {
		$query = "SELECT
                        s.*,
                        sp.win_odds,
                        sp.place_odds,
                        sp.bet_product_id,
                        sp.w_product_id,
                        sp.p_product_id,
                        sp.override_odds,
                        ss.name AS status,
                        sr.win_dividend,
                        sr.place_dividend
			  FROM
			                        `tbdb_selection` AS s
			  INNER JOIN
			                        `tbdb_market` AS m
			  ON
			                        s.market_id = m.id
			  LEFT JOIN
			                        `tbdb_selection_price` AS sp
			  ON
			                        s.id = sp.selection_id
			  LEFT JOIN
			                        `tbdb_selection_result` AS sr
			  ON
			                        sr.selection_id = s.id
			  INNER JOIN
			                        `tbdb_selection_status` AS ss
			  ON
			                        s.selection_status_id = ss.id
			  INNER JOIN
			                        `tbdb_event` AS e
			  ON
			                        m.event_id = e.id
			  WHERE
			                        e.id = '$raceId'
			  ORDER
			                        BY NUMBER ASC";		
	
		$result = \DB::select($query);
		  
		return $result;
		
		}

}