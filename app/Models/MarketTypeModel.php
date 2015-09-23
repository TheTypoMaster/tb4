<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/01/15
 * File creation time: 13:34
 * Project: tb4
 */

use Eloquent;

class MarketTypeModel extends Eloquent
{
    protected $table = 'tbdb_market_type';
    protected $guarded = array();

    /*
     * Relationships
     */
    public function markest()
    {
        return $this->hasMany('TopBetta\Models\Market', 'id', 'market_type_id');
    }

    public function icon()
    {
        return $this->belongsTo('TopBetta\Models\IconModel');
    }

    public function sportDetails($sport)
    {
        return $this->hasMany('TopBetta\Models\SportMarketTypeDetailsModel', 'market_type_id')
            ->where('sport_id', $sport)
            ->first();
    }

    public function markettypegroup()
    {
        return $this->belongsTo('TopBetta\Models\MarketTypeGroup', 'market_type_group_id', 'market_type_group_id');
    }

}