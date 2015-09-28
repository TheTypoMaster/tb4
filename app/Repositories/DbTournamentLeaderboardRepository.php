<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 4/08/2014
 * File creation time: 9:19 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentLeaderboardModel;
use DB;
use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;


class DbTournamentLeaderboardRepository extends BaseEloquentRepository implements TournamentLeaderboardRepositoryInterface
{

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
    public function getTournamentLeaderboard($tournamentID, $limit = 50, $qualified = false){

        $tournamentLeaderboard = $this->getTournamentLeaderboardCollection($tournamentID, $limit, $qualified);

        return $tournamentLeaderboard->toArray();
    }

    public function getTournamentLeaderboardPaginated($tournamentID, $limit = 50){
        $query = $this->model->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_leaderboard.user_id')
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_leaderboard.tournament_id')
            ->join('tbdb_tournament_ticket', function($q) use ($tournamentID) {
                $q->on('tbdb_tournament_ticket.user_id', '=', 'tbdb_users.id')->on('tbdb_tournament_ticket.tournament_id', '=', DB::raw($tournamentID));
            })
            ->where('tbdb_tournament_leaderboard.tournament_id', $tournamentID)
            ->groupBy('tbdb_tournament_leaderboard.id');

        $tournamentLeaderboard = $query->orderBy('qualified', 'DESC')->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
            ->select(DB::raw('tbdb_tournament_leaderboard.id as id, tbdb_users.id as user_id, tbdb_users.username as username, tbdb_tournament_leaderboard.currency as currency, turned_over >= balance_to_turnover as qualified, tbdb_tournament_leaderboard.turned_over as turned_over, tbdb_tournament.start_currency as start_currency, tbdb_tournament.rebuy_currency as rebuy_currency, tbdb_tournament.topup_currency as topup_currency, tbdb_tournament_ticket.rebuy_count as rebuys, tbdb_tournament_ticket.topup_count as topups, tbdb_tournament_leaderboard.balance_to_turnover as balance_to_turnover'))
            ->paginate($limit);

        return $tournamentLeaderboard;
    }

    public function getTournamentLeaderboardInOrder($tournamentID)
    {
        $query = $this->model->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_leaderboard.user_id')
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_leaderboard.tournament_id')
            ->join('tbdb_tournament_ticket', function($q) use ($tournamentID) {
                $q->on('tbdb_tournament_ticket.user_id', '=', 'tbdb_users.id')->on('tbdb_tournament_ticket.tournament_id', '=', DB::raw($tournamentID));
            })
            ->where('tbdb_tournament_leaderboard.tournament_id', $tournamentID)
            ->groupBy('tbdb_tournament_leaderboard.id');

        $tournamentLeaderboard = $query->orderBy('qualified', 'DESC')->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
            ->select(DB::raw('tbdb_tournament_leaderboard.id as id, tbdb_users.id as user_id, tbdb_users.username as username, tbdb_tournament_leaderboard.currency as currency, turned_over >= balance_to_turnover as qualified, tbdb_tournament_leaderboard.turned_over as turned_over, tbdb_tournament.start_currency as start_currency, tbdb_tournament.rebuy_currency as rebuy_currency, tbdb_tournament.topup_currency as topup_currency, tbdb_tournament_ticket.rebuy_count as rebuys, tbdb_tournament_ticket.topup_count as topups, tbdb_tournament_leaderboard.balance_to_turnover as balance_to_turnover'))
            ->get();

        return $tournamentLeaderboard;
    }

    public function getTournamentLeaderboardCollection($tournamentID, $limit = 50, $qualified = false){
        $query = $this->model->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_leaderboard.user_id')
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_leaderboard.tournament_id')
            ->join('tbdb_tournament_ticket', function($q) use ($tournamentID) {
                $q->on('tbdb_tournament_ticket.user_id', '=', 'tbdb_users.id')->on('tbdb_tournament_ticket.tournament_id', '=', DB::raw($tournamentID));
            })
            ->where('tbdb_tournament_leaderboard.tournament_id', $tournamentID)
            ->groupBy('tbdb_tournament_leaderboard.id');

        if($qualified){
            $qualified = 1;
            $query->where('tbdb_tournament_leaderboard.turned_over', '>=', 'tbdb_tournament_leaderboard.balance_to_turnover');
        }else{
            $qualified = 0;
            $query->where('tbdb_tournament_leaderboard.turned_over', '<', 'tbdb_tournament_leaderboard.balance_to_turnover');
        }

        $tournamentLeaderboard = $query->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
            ->take($limit)
            ->select(DB::raw('tbdb_users.id as id, tbdb_users.username as username, tbdb_tournament_leaderboard.currency as currency, '.$qualified.' as qualified, tbdb_tournament_leaderboard.turned_over as turned_over, tbdb_tournament.start_currency as start_currency, tbdb_tournament.rebuy_currency as rebuy_currency, tbdb_tournament.topup_currency as topup_currency, tbdb_tournament_ticket.rebuy_count as rebuys, tbdb_tournament_ticket.topup_count as topups, tbdb_tournament_leaderboard.balance_to_turnover as balance_to_turnover'))
            ->get();

        return $tournamentLeaderboard;
    }

    public function getFullTournamentLeaderboardCollection($tournamentId)
    {
        $query = $this->model->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_leaderboard.user_id')
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_leaderboard.tournament_id')
            ->join('tbdb_tournament_ticket', function($q) use ($tournamentId) {
                $q->on('tbdb_tournament_ticket.user_id', '=', 'tbdb_users.id')->on('tbdb_tournament_ticket.tournament_id', '=', DB::raw($tournamentId));
            })
            ->where('tbdb_tournament_leaderboard.tournament_id', $tournamentId)
            ->groupBy('tbdb_tournament_leaderboard.id');


        $tournamentLeaderboard = $query->orderBy('qualified', 'DESC')->orderBy('tbdb_tournament_leaderboard.currency', 'DESC')
            ->select(DB::raw('tbdb_tournament_leaderboard.id as id, tbdb_users.id as user_id, tbdb_users.username as username, tbdb_tournament_leaderboard.currency as currency, (turned_over >= balance_to_turnover)  as qualified, tbdb_tournament_leaderboard.turned_over as turned_over, tbdb_tournament.start_currency as start_currency, tbdb_tournament.rebuy_currency as rebuy_currency, tbdb_tournament.topup_currency as topup_currency, tbdb_tournament_ticket.rebuy_count as rebuys, tbdb_tournament_ticket.topup_count as topups, tbdb_tournament_leaderboard.balance_to_turnover as balance_to_turnover'))
            ->get();

        return $tournamentLeaderboard;
    }

    public function getAllLeaderboardRecordsForTournament($tournament)
    {
        return $this->model->where('tournament_id', '=', $tournament)->orderBy('currency', 'DESC')->with('tournament')->get();
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


        $leaderboardRecord = $this->model
            ->where('user_id', $userId)
            ->where('tournament_id', $tournamentId)
            ->where('turned_over', '>=', DB::raw('balance_to_turnover'))
            ->first();

        if( !  $leaderboardRecord ) {
            return 0;
        }


        //get the position
        $position = $this->model
            ->where('tournament_id', $tournamentId)
            ->where('turned_over', '>=', DB::raw('balance_to_turnover'))
            ->where('currency', '>', $leaderboardRecord->currency)
            ->count();

        return $position + 1;
    }

    public function getLeaderboardRecordsForTournamentWithCurrencyGreaterThen($tournamentId, $currency, $onlyQualified = true)
    {
        $model  = $this->model
            ->where('tournament_id', $tournamentId)
            ->where('currency', '>', $currency);

        if( $onlyQualified ) {
            $model->where('turned_over', '>=', DB::raw('balance_to_turnover'));
        }

        return $model->get();
    }
}