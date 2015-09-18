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

    public function betType() {
        return $this->belongsTo('TopBetta\Models\BetTypeModel', 'bet_type_id');
    }

    public function ticket()
    {
        return $this->belongsTo('TopBetta\Models\TournamentTicketModel', 'tournament_ticket_id');
    }

    public function type()
    {
        return $this->belongsTo('TopBetta\Models\BetTypeModel', 'bet_type_id');
    }

    // --- Selection relationships to match BetModel ---

    public function selection()
    {
        return $this->belongsToMany('TopBetta\Models\SelectionModel', 'tbdb_tournament_bet_selection', 'tournament_bet_id', 'selection_id');
    }

    public function betselection()
    {
        return $this->hasMany('TopBetta\Models\TournamentBetSelectionModel', 'tournament_bet_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('TopBetta\Models\BetProductModel', 'bet_product_id');
    }

    public function status()
    {
        return $this->belongsTo('TopBetta\Models\BetResultStatusModel', 'bet_result_status_id');
    }

} 