<?php namespace TopBetta;

class SportsMatches extends \Eloquent {
	
	protected $table = 'tbdb_event';	
	
	protected $guarded = array();

	public static $rules = array();
	
	static public function eventExists($eventId) {
		return SportsMatches::where('external_event_id', '=', $eventId) -> pluck('id');
	}
}
