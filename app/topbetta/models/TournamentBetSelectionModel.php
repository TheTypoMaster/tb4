<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 17/08/2014
 * File creation time: 12:19 AM
 * Project: tb4
 */

use Eloquent;

class TournamentBetSelectionModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament_bet_selection';

    /*
     * Model Relationships
     */

    public function betselections(){
        return $this->hasOne('TopBetta\Models\BetModel', 'id', 'bet_id');
    }

    public function selections(){
        return $this->hasOne('TopBetta\Models\SelectionModel', 'id', 'selection_id');
    }

} 