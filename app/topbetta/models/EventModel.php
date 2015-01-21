<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 13:00
 * Project: tb4
 */

use Eloquent;

class EventModel extends Eloquent {

    protected $table = 'tbdb_event';
    protected $guarded = array();

    /*
     * Relationships
     */
    public function competition()
    {
        return $this->belongsToMany('TopBetta\Models\CompetitionModel', 'tbdb_event_group_event', 'event_id', 'event_group_id');
    }

}