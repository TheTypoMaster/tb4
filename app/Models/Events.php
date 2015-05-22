<?php namespace TopBetta\Models;

use Eloquent;

class Events extends Eloquent {

	protected $table = 'tbdb_event';

	protected $guarded = array();

	public static $rules = array();

    public function competitions(){
        return $this->belongsToMany('\TopBetta\Models\SportsComps', 'tbdb_event_group_event', 'event_group_id', 'event_id');
    }

    public function competitionpivot(){
        return $this->belongsTo('\TopBetta\Models\SportEventGroupEvent', 'id', 'event_id');
    }

    public function eventstatus(){
        return $this->belongsTo('\TopBetta\SportEventStatus', 'event_status_id', 'id');
    }

}
