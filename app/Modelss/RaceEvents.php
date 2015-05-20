<?php namespace TopBetta;

class RaceEvent extends \Eloquent {

	protected $table = 'tbdb_event';
        protected $fillable = array('quinella_dividend', 'exacta_dividend', 'trifecta_dividend', 'firstfour_dividend');
	
	
	public function racemeetings(){
		return $this->belongsToMany('TopBetta\RaceMeeting', 'tbdb_event_group_event', 'event_id', 'event_group_id');
	}
	
	static public function nextToJump($limit = 10) {
			
		//TODO: this query is straight from joomla. Rebuild in Eloquent
		$query = "SELECT e.id, e.event_id, e.external_event_id, e.tournament_competition_id, e.external_event_id, e.wagering_api_id, e.event_status_id, e.paid_flag, e.name, e.start_date, e.created_date, e.updated_date, e.distance, e.weather, e.track_condition, e.class, e.number, e.trifecta_pool, e.firstfour_pool, e.exacta_pool, e.quinella_pool, e.trifecta_dividend, e.firstfour_dividend, e.exacta_dividend, e.quinella_dividend, e.external_race_pool_id_list, eg.name AS meeting_name, eg.id AS meeting_id, tc.name AS competition_name, eg.type_code AS type, eg.state
		  FROM `tbdb_event` AS e 
		  INNER JOIN `tbdb_event_group_event` AS ege 
		  ON e.id = ege.event_id 
		  INNER JOIN `tbdb_event_status` AS es 
		  ON e.event_status_id = es.id 
		  INNER JOIN `tbdb_event_group` AS eg 
		  ON ege.event_group_id = eg.id 
		  INNER JOIN `tbdb_tournament_competition` AS tc 
		  ON eg.tournament_competition_id = tc.id 
		  INNER JOIN `tbdb_tournament_sport` AS ts 
		  ON tc.tournament_sport_id = ts.id 
		  WHERE ts.racing_flag = 1
		  AND es.keyword = 'selling'
		  AND e.display_flag = 1 
		  AND e.event_status_id != 7
		  ORDER BY e.start_date ASC 
		  LIMIT 0, $limit";
		  
		  $result = \DB::select($query);
		  
		  return $result;
		  
	}
	
	/**
	 * Get the next jump event ids only
	 * 
	 * Nasty that it needs 2 joins, but we need to reliably determine the race type
	 * from the event group.
	 * 
	 * @param type $limit
	 * @return type
	 */
	static public function nextToJumpEventIds($limit = 10) {
		return static::join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
				->join('tbdb_event_group', 'tbdb_event_group_event.event_group_id', '=', 'tbdb_event_group.id')
				->where('tbdb_event.display_flag', 1)
				->where('tbdb_event.event_status_id', 1)
				->whereIn('tbdb_event_group.type_code', array('R','G','H'))
				->orderBy('tbdb_event.start_date', 'ASC')
				->take($limit)
				->select('tbdb_event.id')
				->get();
	}
	

	/**
	 * Check if a event exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function eventExists($meetingId, $raceNo){
		
		
		//TODO: can this be done outsideuery builder
		return \DB::table('tbdb_event')
		->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
		->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
		->where('tbdb_event_group.external_event_group_id',$meetingId )
		->where('tbdb_event.number',$raceNo)->pluck('tbdb_event.id');
		
		//return $this::where('number', $raceNo)->where('external_event_group_id', '=', $meetingId)->racemeetings;
		//return self::racemeetings;
	}
}