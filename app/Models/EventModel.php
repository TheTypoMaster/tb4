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
        return $this->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAYING || $this->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAID;
    }

    public function teams()
    {
        return $this->belongsToMany('\TopBetta\Models\TeamModel', 'tb_team_tbdb_event', 'tbdb_event_id', 'tb_team_id')->withPivot('team_position');
    }

    public function teamPlayers()
    {
        return $this->hasMany('TopBetta\Models\EventTeamPlayerModel', 'event_id');
    }

}