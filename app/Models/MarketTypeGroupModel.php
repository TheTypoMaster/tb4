<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 24/08/15
 * File creation time: 02:01
 * Project: tb4
 */

use Eloquent;

class MarketTypeGroupModel extends Eloquent
{
    protected $table = 'tbdb_market_group';
    protected $guarded = array();

    /*
     * Relationships
     */
    public function market_types()
    {
        return $this->hasMany('TopBetta\Models\MarketTypeModel', 'market_type_group_id', 'market_type_group_id');
    }

}