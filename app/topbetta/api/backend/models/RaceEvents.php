<?php
class RaceEvent extends Eloquent {

	protected $table = 'tbdb_event';
	
	
	public function racemeetings(){
		return $this->belongsToMany('RaceMeeting', 'tbdb_event_group_event', 'event_id', 'event_group_id');
	}
	

	/**
	 * Check if a event exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function eventExists($meetingId, $raceNo){
		
		
		//TODO: can this be done outsideuery builder
		return DB::table('tbdb_event')
		->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
		->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
		->where('tbdb_event_group.external_event_group_id',$meetingId )
		->where('tbdb_event.number',$raceNo)->pluck('tbdb_event.id');
		
		//return $this::where('number', $raceNo)->where('external_event_group_id', '=', $meetingId)->racemeetings;
		//return self::racemeetings;
	}
	
}