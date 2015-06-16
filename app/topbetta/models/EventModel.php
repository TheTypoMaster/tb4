<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 13:00
 * Project: tb4
 */

use Eloquent;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;

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

    public function eventstatus()
    {
        return $this->belongsTo('TopBetta\Models\EventStatusModel', 'event_status_id', 'id');
    }

    public function markets()
    {
        return $this->hasMany('TopBetta\Models\MarketModel', 'event_id');
    }

    public function isPaying()
    {
        return $this->event_status->keyword == EventStatusRepositoryInterface::STATUS_PAYING || $this->event_status->keyword == EventStatusRepositoryInterface::STATUS_PAID;
    }

}