<?php
namespace TopBetta;
class RaceResult extends \Eloquent {

	protected $table = 'tbdb_selection_result';

	public function getResultsForRaceId($raceId) {

		$result_list = \DB::table('tbdb_selection_result AS sr')
            ->leftJoin('tbdb_selection AS s', 's.id', '=', 'sr.selection_id')
            ->leftJoin('tbdb_selection_price AS sp', 'sp.selection_id', '=', 's.id')
            ->join('tbdb_market AS mk', 'mk.id', '=', 's.market_id')
			->join('tbdb_market_type AS mt', 'mt.id', '=', 'mk.market_type_id')
			->join('tbdb_event AS e', 'e.id', '=', 'mk.event_id')
            ->where('e.id', '=', $raceId)
			->orderBy('sr.position', 'asc')                        
            ->select('sr.id', 'sr.position', 'sr.win_dividend', 'sr.place_dividend', 's.id AS selection_id', 's.number AS runner_number', 's.name AS selection_name', 
	            's.external_selection_id', 'sp.win_odds', 'sp.place_odds', 'sp.override_odds', 'mk.id AS market_id', 'mk.external_market_id', 'e.id AS event_id', 
	            'e.external_event_id', 'e.event_status_id', 'e.paid_flag', 'e.trifecta_dividend', 'e.firstfour_dividend','e.quinella_dividend','e.exacta_dividend',
	            'e.trifecta_pool','e.firstfour_pool','e.quinella_pool','e.exacta_pool','mt.name AS market_name')
            ->get();			


			if (count($result_list) > 0) {
				$exoticList = array(
					'trifecta' => $result_list[0]->trifecta_dividend, 
					'firstfour' => $result_list[0]->firstfour_dividend, 
					'quinella' => $result_list[0]->quinella_dividend, 
					'exacta' => $result_list[0]->exacta_dividend);
	
			} else {
				
				return array();
				
			}				
			
			if (!empty($result_list)) {
				$runner_list		= \TopBetta\RaceSelection::getRunnersForRaceId($raceId, false);
				$runner_list_by_id	= array();
				
				foreach($runner_list as $runner) {
						
					$runner_list_by_id[$runner->id]	= $runner;
					
				}
				
				$result_display_list = $this->_getResultDisplayList($result_list, $runner_list_by_id, $exoticList);
			
				
				// win-place
				$positions = array();
				$count = 1;
				$resultString = "";
								
				foreach ($result_display_list['rank'] as $result) {
						
					if ($count != 1) {
						$resultString .= ($result['position'] == $prevPosition) ? ',' : '/';
					}	
					
					if ($result['win_dividend']) {
							
						$positions[$count] = array('position' => $result['position'], 'number' => $result['number'], 'name' => $result['name'], 'win_dividend' => (float)number_format($result['win_dividend'], 2), 'place_dividend' => (float)number_format($result['place_dividend'], 2));
						
					} else {

						$positions[$count] = array('position' => $result['position'], 'number' => $result['number'], 'name' => $result['name'], 'place_dividend' => (float)number_format($result['place_dividend'], 2));
					
					}					
					
					$resultString .= $result['number'];					
					$prevPosition = $result['position'];					
					
					$count++;
				}
				
				// exotics
				$exotics = array();
				
				foreach ($result_display_list['exotic'] as $type => $exotic_result) {
						
					if (!empty($exotic_result) && is_array($exotic_result)) {
							
						foreach ($exotic_result as $combos => $dividend) {
								
							//$exotics[] = array($type => array("selections" => $combos, "dividend" => (float)$dividend));
							$exotics[] = array("name" => $type, "selections" => $combos, "dividend" => (float)$dividend);	
							
						}
						
					}
					
				}

				$results = array('results_string' => $resultString, 'exotics' => $exotics, 'positions' => $positions);
				
				return $results;
				
			}



	}

	/**
	 * Get the result list for display
	 *
	 * @param $result_list
	 * @param $runner_list
	 * @return void
	 */
	protected function _getResultDisplayList($result_list, $runner_list, $exoticList = null)
	{
		
		$display_result_list = array(
			'dividend_field'	=> 'odds', // for old data before dividends fields introduced
			'has_exotics'		=> false,
			'rank'				=> array(),
			'exotic'			=> array(
				'quinella'		=> array(),
				'exacta'		=> array(),
				'trifecta'		=> array(),
				'firstfour'		=> array(),
			),
		);
		foreach ($result_list as $result) {
			$runner			= $runner_list[$result->selection_id];
			$runner_number	= $runner->number;
			$win_odds		= null;
			$place_odds		= null;
			$win_dividend	= null;
			$place_dividend	= null;
					
			if ($result->position < 4 ) {
				$place_odds		= $runner->place_odds;
				$place_dividend = $result->place_dividend;
			}
			
			if (1 == $result->position) {
				$win_odds		= $runner->win_odds;
				$win_dividend	= $result->win_dividend;
				
				if($win_dividend > 0) {
					$display_result_list['dividend_field'] = 'dividend';
				}
			}
			
			$display_result_list['rank'][] = array(
				'rank_no'			=> $result->position,
				'position'			=> $result->position,
				'number'			=> $runner->number,
				'name'				=> $runner->name,
				'win_odds'			=> $win_odds,
				'place_odds'		=> $place_odds,
				'win_dividend'		=> $win_dividend,
				'place_dividend'	=> $place_dividend
			);
		}

		if ($exoticList) {
			foreach ($exoticList as $exotic_type => $val) {
				$dividends = unserialize($val);

				$display_result_list['exotic'][$exotic_type] = $dividends;
				
				if ($dividends > 0) {
					$display_result_list['has_exotics'] = true;
				}
			}
		}
		

		return $display_result_list;
	}

	private function toCents($amount) {
			
		return (int)($amount * 100);
		
	}


    static public function deleteResultsForRaceId($raceId) {

        return \DB::statement('DELETE sr.* FROM tbdb_selection_result as sr INNER JOIN tbdb_selection as s on s.id = selection_id INNER JOIN tbdb_market as mk on mk.id = s.market_id INNER JOIN tbdb_event as e on e.id = mk.event_id WHERE e.id ='. $raceId);

    }
}
