<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentBetSelectionModel;

class DbTournamentBetSelectionRepository extends BaseEloquentRepository {

    protected $model;

    public function __construct(TournamentBetSelectionModel $tournamentbetselections){
        $this->model = $tournamentbetselections;
    }

    /**
     * @param $ticketId
     * @return mixed
     */
    public function getSelectionResultsForBetSelection($betId){

//        return $this->model->where('tournament_bet_id', $betId)->selectionresults();
        return $this->model->with('selectionresults')->where('tournament_bet_id', $betId)->get();
//
//        return $this->model->where('tournament_ticket_id', $ticketId)
//                            ->get();
//                           // ->where('bet_result_status_id', $status)->get();
    }

    public function getBetSelectionId($betId){
        return $this->model->where('tournament_bet_id', $betId)
                            ->value('selection_id');
    }

} 