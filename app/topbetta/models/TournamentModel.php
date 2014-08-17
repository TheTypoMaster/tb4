<?php namespace TopBetta\Models;

use Eloquent;

class TournamentModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament';


	/*
	 * Model relationships
	 */

    public function tournamentlabels(){
        return $this->belongsToMany('TopBetta\TournamentLabels', 'tb_tournament_label_tournament', 'tournament_id', 'tournament_label_id');
    }
	
	public function parentTournament() {
		return $this->belongsTo('TopBetta\Tournament', 'parent_tournament_id');
	}
	
	public function eventGroup() {
		return $this->belongsTo('TopBetta\RaceMeeting', 'event_group_id');
	}
	
	public function sport() {
		return $this->belongsTo('TopBetta\SportsSportName', 'tournament_sport_id');
	}

  	public function leaderboards() {
		return $this->hasMany('\TopBetta\TournamentLeaderboard', 'tournament_id');
	}
}
