<?php namespace TopBetta\Models;

use Carbon\Carbon;
use Eloquent;

class TournamentModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament';

	/*
	 * Model relationships
	 */

    public function tournamentlabels(){
        return $this->belongsToMany('TopBetta\Models\TournamentLabels', 'tb_tournament_label_tournament', 'tournament_id', 'tournament_label_id');
    }
	
	public function parentTournament() {
		return $this->belongsTo('TopBetta\Models\Tournament', 'parent_tournament_id');
	}
	
	public function eventGroup() {
		return $this->belongsTo('TopBetta\Models\RaceMeeting', 'event_group_id');
	}
	
	public function sport() {
		return $this->belongsTo('TopBetta\Models\SportsSportName', 'tournament_sport_id');
	}

    public function tickets()
    {
        return $this->hasMany('TopBetta\Models\TournamentTicketModel', 'tournament_id');
    }

    public function groups()
    {
        return $this->belongsToMany('TopBetta\Models\TournamentGroupModel', 'tb_tournament_group_tournament', 'tournament_id', 'tournament_group_id');
    }

  	public function leaderboards() {
		return $this->hasMany('TopBetta\Models\TournamentLeaderboard', 'tournament_id');
	}

    public function competition()
    {
        return $this->belongsTo('TopBetta\Models\CompetitionModel', 'event_group_id');
    }

    public function bettingClosed()
    {
        return $this->cancelled_flag || ($this->betting_closed_on_first_match_flag && Carbon::now() > $this->betting_closed_date);
    }

    public function entryClosed()
    {
        return $this->bettingClosed() || $this->end_date < Carbon::now() || ($this->entries_close && $this->entries_close != '0000-00-00 00:00:00' && $this->entries_close < Carbon::now());
    }
	
	public function qualifiers()
    {
        return $this->leaderboards()->whereRaw('balance_to_turnover <= turned_over')->where('currency', '>', 0);
    }

    public function prizeFormat()
    {
        return $this->belongsTo('TopBetta\Models\TournamentPrizeFormat', 'tournament_prize_format');
    }

    public function comments()
    {
        return $this->hasMany('TopBetta\Models\TournamentCommentModel', 'tournament_id');
    }

    public function prizePool()
    {
        $amount = $this->tickets->sum(function($v) {
            return $v->rebuy_count * $this->rebuy_buyin + $v->topup_count * $this->topup_buyin + $this->buy_in;
        });

        return max($amount, $this->minimum_prize_pool);
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