<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 13:00
 * Project: tb4
 */

use Eloquent;

class MarketModel extends Eloquent {

    protected $table = 'tbdb_market';
    protected $guarded = array();

    /*
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo('TopBetta\Models\EventModel', 'event_id', 'id');
    }

    public function markettype()
    {
        return $this->belongsTo('TopBetta\Models\MarketTypeModel', 'market_type_id', 'id');
    }

    public function selections()
    {
        return $this->hasMany('TopBetta\Models\SelectionModel', 'market_id');
    }

    public function result()
    {
        $model = $this->selections()->has('result');

        if( $model->first() ) {
            return $model->first()->result();
        }

        return $model;
    }


}