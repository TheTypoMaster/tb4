<?php namespace TopBetta\Models;

use Eloquent;

class Events extends Eloquent {

	protected $table = 'tbdb_event';

	protected $guarded = array();

	public static $rules = array();

    public function competitions(){
        return $this->belongsToMany('\TopBetta\SportsComps', 'tbdb_event_group_event', 'event_group_id', 'event_id');
    }

}
