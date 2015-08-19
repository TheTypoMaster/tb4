<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 12:20
 * Project: tb4
 */

use Eloquent;

class CompetitionModel extends Eloquent {

    protected $table = 'tbdb_event_group';
    protected $guarded = array();
    public static $rules = array();

    /*
     * Relationships
     */

    /**
     * @return mixed
     */
    public function sport(){
        return $this->belongsTo('\TopBetta\Models\SportModel', 'sport_id', 'id');
    }

    /**
     * @return mixed
     */
    public function events(){
        return $this->belongsToMany('TopBetta\Models\EventModel', 'tbdb_event_group_event', 'event_group_id', 'event_id');
    }

    /**
     * Pseudonym for events due to events field on event_group table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function competitionEvents() {
        return $this->belongsToMany('TopBetta\Models\EventModel', 'tbdb_event_group_event', 'event_group_id', 'event_id');
    }

    public function baseCompetition() {
        return $this->belongsTo('TopBetta\Models\BaseCompetitionModel', 'base_competition_id');
    }

    public function icon() {
        return $this->belongsTo('TopBetta\Models\IconModel');
    }

    public function defaultEventIcon() {
        return $this->belongsTo('TopBetta\Models\IconModel', 'default_event_icon_id');
    }

    public function tournamentMarketTypes()
    {
        return $this->belongsToMany('TopBetta\Models\MarketTypeModel', 'tbdb_event_group_market_type', 'event_group_id', 'market_type_id');
    }

}