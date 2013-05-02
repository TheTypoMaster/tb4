<?php

use Doctrine\Tests\DBAL\Types\IntegerTest;

class RaceMeeting extends Eloquent {

	protected $table = 'tbdb_event_group';
	
	
	public function raceevents(){
		return $this->belongsToMany('RaceEvent', 'tbdb_event_group_event', 'event_group_id', 'event_id');
	}
	
	
	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function meetingExists($meetingId){
		return RaceMeeting::where('external_event_group_id', '=', $meetingId)->pluck('id');
	} 
	

}