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

    public function calculateTournamentPrizePool($tournamentId) {
        $tournament = TournamentModel::find($tournamentId);

        $query = 'SELECT
				t.buy_in AS buy_in,
				COUNT(tt.user_id) AS entrants
			FROM
				tbdb_tournament_ticket AS tt
			INNER JOIN
				tbdb_tournament AS t
			ON
				tt.tournament_id = t.id
			WHERE
				tt.tournament_id = ' . $tournamentId . '
			AND
				tt.refunded_flag = 0
			GROUP BY
				tt.tournament_id';

        $result = \DB::select($query);

        $current_prize_pool = empty($result) ? 0 : ($result[0] -> buy_in) * $result[0] -> entrants;
        return ($current_prize_pool > $tournament -> minimum_prize_pool) ? $current_prize_pool : $tournament -> minimum_prize_pool;
    }
}
