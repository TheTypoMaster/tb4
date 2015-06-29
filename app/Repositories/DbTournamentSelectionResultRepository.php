<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentSelectionResultModel;

class DbTournamentSelectionResultRepository extends BaseEloquentRepository {

    protected $model;

    public function __construct(TournamentSelectionResultModel $tournamentselectionresults){
        $this->model = $tournamentselectionresults;
    }
//
//    /**
//     * @param $ticketId
//     * @return mixed
//     */
//    public function getResultedUserBetsInTournament($ticketId){
//        return $this->model->where('tournament_ticket_id', $ticketId)
//                            ->get();
//                           // ->where('bet_result_status_id', $status)->get();
//    }

    public function getSelectionResultForSelectionId($selectionId){
        return $this->model->where('selection_id', $selectionId)
                            ->first();
    }
} 