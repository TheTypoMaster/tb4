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
        return $this->hasOne('TopBetta\Models\SelectionPricesModel', 'selection_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany('TopBetta\Models\SelectionPricesModel', 'selection_id', 'id');
    }

    public function result()
    {
        return $this->hasOne('TopBetta\Models\SelectionResultModel', 'selection_id', 'id');
    }





    public function sportsResults()
    {
        return $this->hasOne('TopBetta\Models\SportsResults', 'selection_id', 'id');
    }

    public function selectionprice()
    {
        return $this->hasOne('TopBetta\Models\SportsSelectionPrice', 'selection_id', 'id');
    }

    public function selectionresult()
    {
        return $this->hasOne('TopBetta\Models\SportsSelectionResults', 'selection_id', 'id');
    }

    public function selectionstatus()
    {
        return $this->belongsTo('TopBetta\Models\SportsSelectionStatus', 'selection_status_id', 'id');
    }

    public function team()
    {
        return $this->morphedByMany('TopBetta\Models\TeamModel', 'competitor', 'tb_selection_competitor', 'selection_id');
    }

    public function player()
    {
        return $this->morphedByMany('TopBetta\Models\PlayerModel', 'competitor', 'tb_selection_competitor', 'selection_id');
    }

    public function runner()
    {
        return $this->belongsTo('TopBetta\Models\RunnerModel', 'runner_id');
    }

    public function form()
    {
        return $this->belongsTo('TopBetta\Models\RisaForm', 'runner_code', 'runner_code');
    }

    public function lastStarts()
    {
        return $this->hasMany('TopBetta\Models\LastStartsModel', 'runner_code', 'runner_code');
    }

} 