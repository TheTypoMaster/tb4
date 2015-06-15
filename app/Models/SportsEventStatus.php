<?php namespace TopBetta\Models;

class SportEventStatus extends \Eloquent {

	protected $table = 'tbdb_event_status';
	
	static public function getSportsEventStatusIdByKeyword($keyword){
		return SportEventStatus::where('keyword', $keyword)->value('id');
	}
	
}