<?php
class RaceSelection extends Eloquent {

	protected $table = 'tbdb_selection';
	
	/**
	 * Check if a selection exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function selectionExists($meetingId, $raceNo, $runnerNo){
	
	
		//TODO: can this be done outsideuery builder
		return DB::table('tbdb_selection')
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
	
	

}