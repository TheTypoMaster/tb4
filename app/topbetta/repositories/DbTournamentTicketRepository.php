<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentTicketModel;

class DbTournamentTicketRepository extends BaseEloquentRepository {

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

} 