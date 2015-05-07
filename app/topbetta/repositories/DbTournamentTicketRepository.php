<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

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

} 