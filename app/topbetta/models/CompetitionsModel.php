<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 12:20
 * Project: tb4
 */

use Eloquent;

class CompetitionsModel extends Eloquent {

    protected $table = 'tbdb_event_group';
    protected $guarded = array();
    public static $rules = array();

    /*
     * Relationships
     */

    /**
     * @return mixed
     */
    public function sports(){
        return $this->belongsTo('\TopBetta\TournamentSport', 'sport_id', 'id');
    }

    /**
     * @return mixed
     */
    public function events(){
        return $this->belongsToMany('TopBetta\Models\Events', 'tbdb_event_group_event', 'event_id', 'event_group_id');
    }

}