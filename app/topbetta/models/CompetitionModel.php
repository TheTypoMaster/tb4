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

    public function tournamentMarketTypes()
    {
        return $this->belongsToMany('TopBetta\Models\MarketTypeModel', 'tbdb_event_group_market_type', 'event_group_id', 'market_type_id');
    }

}