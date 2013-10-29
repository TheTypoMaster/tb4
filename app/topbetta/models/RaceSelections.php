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
                        sr.place_dividend,
                        rrf.age as df_age,
                        rrf.colour as df_colour,
                        rrf.sex as df_sex,
                        rrf.career_results as df_career,
                        rrf.track_results as df_tracks,
                        rrf.track_distance_results as df_track_distance,
                        rrf.first_up_results as df_first_up,
                        rrf.second_up_results as df_second_up,
                        rrf.good_results as df_good,
                        rrf.dead_results as df_dead,
                        rrf.slow_results as df_slow,
                        rrf.heavy_results as df_heavy,
                        fls.runner_form_id as ls_id,
                        fls.finish_position as ls_finish_position,
                        fls.race_starters as ls_race_starters,
                        fls.abr_venue as ls_abr_venue,
                        fls.race_distance as ls_race_distance,
                        fls.name_race_form as ls_name_race_form,
                        fls.mgt_date as ls_mgt_date,
                        fls.track_condition as ls_track_condition,
                        fls.numeric_rating as ls_numeric_rating,
                        fls.jockey_initials as ls_jockey_initials,
                        fls.jockey_surname as ls_jockey_surname,
                        fls.handicap as ls_handicap,
                        fls.barrier as ls_barrier,
                        fls.starting_win_price as ls_starting_win_price,
                        fls.other_runner_name as ls_other_runner_name,
                        fls.other_runner_barrier as ls_other_runner_barrier,
                        fls.in_running_800 as ls_in_running_800,
                        fls.in_running_400 as ls_in_running_400,
                        fls.other_runner_time as ls_other_runner_time,
                        fls.margin_decimal as ls_margin_decimal
                         
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
			  LEFT JOIN
			  						`tb_data_risa_runner_form` as rrf
			  ON
			  						rrf.runner_code = s.runner_code
			  LEFT JOIN
			  						`tb_data_risa_runner_form_last_starts` as fls
			  ON
			  						fls.runner_form_id = rrf.id
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

			$result[] = array('id' => (int)$runner -> id, 'name' => $runner -> name, 'jockey' => $runner -> associate, 'trainer' => $runner -> trainer, 'weight' => (float)$runner -> weight, 'saddle' => (int)$runner -> number, 'barrier' => (int)$runner -> barrier, 'scratched' => $scratched, 'form' => $runner -> last_starts, 'pricing' => $pricing, 'risa_silk_id' => $runner -> silk_id);

		}

		return $result;
	}	


}