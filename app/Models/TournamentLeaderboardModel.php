<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 17/08/2014
 * File creation time: 10:47 PM
 * Project: tb4
 */

use Eloquent;

class TournamentLeaderboardModel extends Eloquent{

    protected $guarded = array();
    protected $table = 'tbdb_tournament_leaderboard';
    public static $rules = array();
    public $timestamps = false;

    public function tournament()
    {
        return $this->belongsTo('TopBetta\Models\TournamentModel', 'tournament_id');
    }

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id');
    }

    public function ticket()
    {
        return $this->hasOne('TopBetta\Models\TournamentTicketModel', 'tournament_id', 'tournament_id')
            ->where('user_id', $this->user_id);
    }
} 