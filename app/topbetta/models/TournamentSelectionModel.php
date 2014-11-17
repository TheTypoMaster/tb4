<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 17/08/2014
 * File creation time: 12:19 AM
 * Project: tb4
 */

use Eloquent;

class TournamentSelectionModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_selection';

    /*
     * Model Relationships
     */

    public function betselections(){
        return $this->hasMany('TopBetta\Models\BetSelectionModel', 'selection_id', 'id');
    }

    public function selectionresults(){
        return $this->hasOne('TopBetta\Models\SelectionResultModel', 'selection_id', 'id');
    }

} 