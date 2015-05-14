<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:27
 * Project: tb4
 */

use Eloquent;

class BetSelectionModel extends Eloquent {

    protected $table = 'tbdb_bet_selection';
    protected $guarded = array();
    public $timestamps = false;

    public function selection() {
        return $this->belongsTo('TopBetta\Models\SelectionModel', 'selection_id', 'id');
    }

}