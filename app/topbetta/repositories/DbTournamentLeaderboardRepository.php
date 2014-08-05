<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 4/08/2014
 * File creation time: 9:19 PM
 * Project: tb4
 */

use TopBetta\TournamentLeaderboard;
use DB;


class TournamentLeaderboardRepository extends BaseEloquentRepository{

    protected $tournamentLeaderboard;


    function __construct(TournamentLeaderboard $tournamentLeaderboard) {
        $this->model = $tournamentLeaderboard;

    }

    /**
     * getTournamentLeaderboard get the current tournament leaderboard
     * @param $tournamentID
     * @param int $limit
     * @param $startCurrency
     * @param bool $qualified
     * @return mixed
     */
    public function getTournamentLeaderboard($tournamentID, $limit = 50, $startCurrency, $qualified = false){
        $query = $this->model->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_leaderboard.user_id')
                            ->where('tbdb_tournament_leaderboard.tournament_id', $tournamentID);

        if($qualified){
            //$qualified = 1;
            $query->where('tbdb_tournament_leaderboard.turned_over', '>=', $startCurrency);
        }else{
            //$qualified = 0;
            $query->where('tbdb_tournament_leaderboard.turned_over', '<', $startCurrency);
        }

        $tournamentLeaderboard = $query->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
                                        ->take($limit)
                                        ->select(DB::raw('tbdb_users.id as id, tbdb_users.username as username, tbdb_tournament_leaderboard.currency as currency, '.$qualified.' as qualified'))
                                        ->get()
                                        ->toArray();

        return $tournamentLeaderboard;
    }
}