<?php namespace TopBetta;

use Illuminate\Database\Eloquent;

class TournamentLabelTournament extends Eloquent {
    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tb_tournament_label_tournament';

}