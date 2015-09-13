<?php namespace TopBetta\Models;

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
					->where('tbdb_selection.number', $runnerNo)->value('tbdb_selection.id');
	
		//return $this::where('number', $raceNo)->where('external_event_group_id', '=', $meetingId)->racemeetings;
		//return self::racemeetings;
	}
	/**
	 * Get the list of runners for a race id
	 *
	 * @param integer $raceId
	 * @return array
	 */
	public static function getRunnersForRaceId($raceId, $formatted = true) {
			
		//TODO: this query is straight from joomla. Rebuild in Eloquent
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
	
		$runners = \DB::select($query);
		
		if ($formatted == false) {
			return $runners;
		}

		$result = array();

		foreach ($runners as $runner) {
			$scratched = ($runner -> status == "Scratched") ? true : false;
			$pricing = array('win' => (float)number_format($runner -> win_odds, 2), 'place' => (float)number_format($runner -> place_odds, 2));

            // dirty....
            if($pricing['win'] == '0.01' ) $pricing['win'] = '';
            if($pricing['place'] == '0.01' ) $pricing['place'] = '';

			$result[] = array('id' => (int)$runner -> id, 'external_runner_id' =>  $runner->external_selection_id, 'name' => $runner -> name, 'jockey' => $runner -> associate, 'trainer' => $runner -> trainer, 'weight' => (float)$runner -> weight, 'saddle' => (int)$runner -> number, 'barrier' => (int)$runner -> barrier, 'scratched' => $scratched, 'form' => $runner -> last_starts, 'pricing' => $pricing, 'risa_silk_id' => $runner -> silk_id, 'runner_code' => $runner->runner_code);

		}

		return $result;
	}
        
        public static function getByEventIdAndRunnerNumber($eventId, $runnerNumber)
        {
            return static::where('tbdb_selection.number', $runnerNumber)
                            ->where('tbdb_event.external_event_id', $eventId)
                            ->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
                            ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event.id')
                            ->take(1)
                            ->select('tbdb_selection.*')
                            ->get();
        }

}