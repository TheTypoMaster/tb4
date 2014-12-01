<?php namespace TopBetta\Models;

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/11/14
 * File creation time: 08:48
 * Project: tb4
 */

use Eloquent;

class SelectionsModel extends Eloquent{

    protected $table = 'tbdb_selection';
    protected $guarded = array();
    public static $rules = array();

    /*
     * Relationships
     */
    public function markets()
    {
        return $this->belongsTo('TopBetta\SportsMarket', 'market_id', 'id');
    }

    public function sportsResults()
    {
        return $this->hasOne('TopBetta\SportsResults', 'selection_id', 'id');
    }

    public function selectionprice()
    {
        return $this->hasOne('TopBetta\SportsSelectionPrice', 'selection_id', 'id');
    }

    public function selectionresult()
    {
        return $this->hasOne('TopBetta\SportsSelectionResults', 'selection_id', 'id');
    }

    public function selectionstatus()
    {
        return $this->belongsTo('TopBetta\Models\SportsSelectionStatus', 'selection_status_id', 'id');
    }

} 