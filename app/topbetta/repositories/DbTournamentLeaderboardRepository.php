<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 4/08/2014
 * File creation time: 9:19 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentLeaderboardModel;
use DB;


class DbTournamentLeaderboardRepository extends BaseEloquentRepository{

    protected $tournamentLeaderboard;


    function __construct(TournamentLeaderboardModel $tournamentLeaderboard) {
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
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_leaderboard.tournament_id')
            ->join('tbdb_tournament_ticket', function($q) use ($tournamentID) {
                $q->on('tbdb_tournament_ticket.user_id', '=', 'tbdb_users.id')->on('tbdb_tournament_ticket.tournament_id', '=', DB::raw($tournamentID));
            })
            //get rebuys
            ->leftJoin('tbdb_tournament_ticket_buyin_history as tbh_rebuys', function($q) {
                $q->on('tbdb_tournament_ticket.id', '=', 'tbh_rebuys.tournament_ticket_id')->on('tbh_rebuys.tournament_buyin_type_id', '=', DB::raw(2));
            })
            //get topups
            ->leftJoin('tbdb_tournament_ticket_buyin_history as tbh_topups', function($q) {
                $q->on('tbdb_tournament_ticket.id', '=', 'tbh_topups.tournament_ticket_id')->on('tbh_topups.tournament_buyin_type_id', '=', DB::raw(3));
            })
            ->where('tbdb_tournament_leaderboard.tournament_id', $tournamentID)
            ->groupBy('tbdb_tournament_leaderboard.id');

        if($qualified){
            $qualified = 1;
            //$query->where('tbdb_tournament_leaderboard.turned_over', '>=', $startCurrency);
            $query->havingRaw('tbdb_tournament_leaderboard.turned_over >= (COUNT(tbh_rebuys.id) * tbdb_tournament.rebuy_currency + COUNT(tbh_topups.id) * tbdb_tournament.topup_currency + tbdb_tournament.start_currency)');
        }else{
            $qualified = 0;
            //$query->where('tbdb_tournament_leaderboard.turned_over', '<', $startCurrency);
            $query->havingRaw('tbdb_tournament_leaderboard.turned_over < (COUNT(tbh_rebuys.id) * tbdb_tournament.rebuy_currency + COUNT(tbh_topups.id) * tbdb_tournament.topup_currency + tbdb_tournament.start_currency)');
        }

        $tournamentLeaderboard = $query->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
            ->take($limit)
            ->select(DB::raw('tbdb_users.id as id, tbdb_users.username as username, tbdb_tournament_leaderboard.currency as currency, '.$qualified.' as qualified, tbdb_tournament_leaderboard.turned_over as turned_over, tbdb_tournament.start_currency as start_currency, tbdb_tournament.rebuy_currency as rebuy_currency, tbdb_tournament.topup_currency as topup_currency'))
            ->get()
            ->toArray();

        return $tournamentLeaderboard;
    }

    public function getLeaderboardRecordForUserInTournament($userId, $tournamentId){
        return $this->model->where('user_id', $userId)
                            ->where('tournament_id', $tournamentId)
                            ->first();
    }

    public function updateLeaderboardRecordForUserInTournament($userId, $tournamentId, $turnover, $currency){
        $leaderboardModel =  $this->model->where('user_id', $userId)
            ->where('tournament_id', $tournamentId)
            ->first();

        if($leaderboardModel){
            $leaderboardModel->turned_over = $turnover;
            $leaderboardModel->currency = $currency;
            return $leaderboardModel->save();
        }
        return false;
    }

    public function getLeaderBoardPositionForUser($userId, $tournamentId, $qualified = true)
    {
        if( $qualified ) {
            //make sure user has qualified

            $leaderboardRecord = $this->model
                ->where('user_id', $userId)
                ->where('tournament_id', $tournamentId)
                ->whereHas('tournament', function($q) {
                    $q->where('turned_over', '>=', DB::raw('start_currency'));
                })
                ->first();

            if( !  $leaderboardRecord ) {
                return 0;
            }
        }

        //get the position
        $position = $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_leaderboard.tournament_id')
            ->where('tournament_id', $tournamentId)
            ->where('currency', '>', function($q) use ($userId, $tournamentId) {
                $q->from('tbdb_tournament_leaderboard')
                    ->where('user_id', $userId)
                    ->where('tournament_id', $tournamentId)
                    ->select('currency');
            });

        if( $position ) {
            $position->where('turned_over', '>=', 'start_currency');
        }

        return $position->count() + 1;
    }
}