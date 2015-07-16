<?php namespace TopBetta\Models;
class SportEventGroupEvent extends \Eloquent {

	protected $table = 'tbdb_event_group_event';

    protected $primaryKey = 'ege_id';

	/**
	 * Check if a event group event exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function eventGEExists($eventId, $compId){
		return SportEventGroupEvent::where('event_id', '=', $eventId )
									  ->where('event_group_id', '=', $compId )->value('event_id');
	}

    public function competitions(){
        return $this->belongsTo('\TopBetta\Models\SportsComps', 'event_group_id', 'id');
    }

    public function events(){
            return $this->belongsTo('\TopBetta\Models\Events', 'event_id', 'id');
    }
}