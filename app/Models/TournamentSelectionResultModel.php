<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 17/08/2014
 * File creation time: 12:19 AM
 * Project: tb4
 */

use Eloquent;

class TournamentSelectionResultModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_selection_result';


    public function selections(){
        return $this->belongsTo('TopBetta\Models\SelectionModel', 'id', 'selection_id');
    }

}