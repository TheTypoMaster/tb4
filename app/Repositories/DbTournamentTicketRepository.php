<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Models\TournamentTicketModel;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;

class DbTournamentTicketRepository extends BaseEloquentRepository implements TournamentTicketRepositoryInterface
{

    protected $model;

    public function __construct(TournamentTicketModel $tournamenttickets){
        $this->model = $tournamenttickets;
    }

    /**
     * @param $tournamentId
     * @return mixed
     */
    public function getTicketsInTournament($tournamentId){
        return $this->model->where('tournament_id', $tournamentId)->get();
    }

    public function getWithUserAndTournament($ticketId)
    {
        return $this->model
            ->where('id', $ticketId)
            ->with(array('user', 'user.topbettauser', 'tournament'))
            ->first()->toArray();
    }

    public function getTicketByUserAndTournament($userId, $tournamentId)
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->where('tournament_id', $tournamentId)
            ->with('tournament')
            ->first();
    }

    public function getLimitedFreeTicketsForUserBetween($userId, $start, $end)
    {
        return $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
            ->where('user_id', $userId)
            ->where('start_date', '>=', $start)
            ->where('start_date', '<=', $end)
            ->where('buy_in', 0)
            ->where('free_tournament_buyin_limit_flag', true)
            ->get(array('tbdb_tournament_ticket.*'));
    }

    public function getRecentAndActiveTicketsForUserWithTournament($user)
    {
        return $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
            ->where('user_id', $user)
            ->where(function($q) {
                $q->where('tbdb_tournament.paid_flag', false)
                    ->orWhere('tbdb_tournament.end_date', '>=', Carbon::now()->startOfDay());
            })
            ->with(array('bets', 'tournament'))
            ->get(array('tbdb_tournament_ticket.*'))
            ->load('leaderboard');
    }

    public function nextToJumpTicketsForUser($user, $limit = 10)
    {
        return $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
            ->join('tbdb_event_group as eg', 'eg.id', '=', 'tbdb_tournament.event_group_id')
            ->join('tbdb_event_group_event as ege', 'ege.event_group_id', '=', 'eg.id')
            ->join('tbdb_event as e', 'e.id', '=', 'ege.event_id')
            ->where('user_id', $user)
            ->where('e.start_date', '>=', Carbon::now())
            ->orderBy('e.start_date')
            ->groupBy('tbdb_tournament_ticket.id')
            ->take($limit)
            ->with(array('bets', 'tournament'))
            ->get(array('tbdb_tournament_ticket.*', 'e.name as event_name', 'e.id as event_id', 'e.start_date as event_start_date', 'eg.name as event_group_name', 'eg.id as event_group_id'))
            ->load('leaderboard');
    }

    public function getActiveTicketsForUser($user)
    {
        return $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
            ->where('user_id', $user)
            ->where('tbdb_tournament.paid_flag', false)
            ->with(array('bets'))
            ->get(array('tbdb_tournament_ticket.*'))
            ->load('leaderboard');
    }

    public function getTicketsForUserOnDate($user, Carbon $date)
    {
        return $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
            ->where('user_id', $user)
            ->where('tbdb_tournament.start_date', '>=', $date->startOfDay()->toDateTimeString())
            ->where('tbdb_tournament.start_date', '<=', $date->endOfDay()->toDateTimeString())
            ->with(array('bets'))
            ->get(array('tbdb_tournament_ticket.*'))
            ->load('leaderboard');
    }

    public function getTicketsForUserByEndDate($user, Carbon $date)
    {
        return $this->model
            ->join('tbdb_tournament', 'tbdb_tournament.id', '=', 'tbdb_tournament_ticket.tournament_id')
            ->where('user_id', $user)
            ->where('tbdb_tournament.end_date', '>=', $date->startOfDay()->toDateTimeString())
            ->where('tbdb_tournament.end_date', '<=', $date->endOfDay()->toDateTimeString())
            ->with(array('bets'))
            ->get(array('tbdb_tournament_ticket.*'))
            ->load('leaderboard');
    }

    public function getAllForUserPaginated($user)
    {
        return $this->model
            ->where('user_id', $user)
            ->with(array('bets'))
            ->paginate();
    }

    public function getByResultTransaction($transaction)
    {
        return $this->model
            ->where('result_transaction_id', $transaction)
            ->first();
    }

} 