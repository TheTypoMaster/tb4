<?php namespace TopBetta\Models;

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/11/14
 * File creation time: 08:48
 * Project: tb4
 */

use Eloquent;

class SelectionModel extends Eloquent{

    protected $table = 'tbdb_selection';
    protected $guarded = array();
    public static $rules = array();

    /*
     * Relationships
     */
    public function market()
    {
        return $this->hasOne('TopBetta\Models\MarketModel', 'id', 'market_id');
    }

    public function price()
    {
        return $this->hasOne('TopBetta\Models\SelectionPricesModel', 'selecion_id', 'id');
    }

    public function result()
    {
        return $this->hasOne('TopBetta\Models\SelectionResultModel', 'selecion_id', 'id');
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

    public function team()
    {
        return $this->morphedByMany('TopBetta\Models\TeamModel', 'competitor', 'tb_selection_competitor', 'selection_id');
    }

} 