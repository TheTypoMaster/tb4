<?php namespace TopBetta;

class TournamentCompetition extends \Eloquent {
	
	protected $table = 'tbdb_tournament_competition';
	
    protected $guarded = array();

    public static $rules = array();
    
    static public function tournamentCompetitionExists($name, $sportID) {
    	return TournamentCompetition::where('name', '=', $name)->where('sport_id', '=', $sportID) 
    										-> pluck('id');
    }
    
}