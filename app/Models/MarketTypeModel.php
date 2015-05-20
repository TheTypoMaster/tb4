<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/01/15
 * File creation time: 13:34
 * Project: tb4
 */

use Eloquent;

class MarketTypeModel extends Eloquent {
    protected $table = 'tbdb_market_type';
    protected $guarded = array();

    /*
     * Relationships
     */
    public function markest()
    {
        return $this->hasMany('TopBetta\Models\Market', 'id', 'market_type_id');
    }
}