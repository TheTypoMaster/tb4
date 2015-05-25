<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 17/08/2014
 * File creation time: 12:19 AM
 * Project: tb4
 */

use Eloquent;

class TournamentBetModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament_bet';
    public $timestamps = false;

    /*
     * Model Relationships
     */

    public function selections()
    {
        return $this->belongsToMany('TopBetta\Models\SelectionModel', 'tbdb_tournament_bet_selection', 'tournament_bet_id', 'selection_id');
    }

    public function betselections(){
        return $this->hasMany('TopBetta\Models\TournamentBetSelectionModel', 'tournament_bet_id', 'id');
    }
} 