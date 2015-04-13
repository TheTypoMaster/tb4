<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentModel;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;

class DbTournamentRepository extends BaseEloquentRepository implements TournamentRepositoryInterface{

    protected $model;

    public function __construct(TournamentModel $tournaments){
        $this->model = $tournaments;
    }

    public function updateTournamentByEventGroupId($eventGroupId, $closeDate){
        return $this->model->where('event_group_id', $eventGroupId)
                    ->update(array('betting_closed_date' => $closeDate, 'end_date' => $closeDate));
    }

	public function getTournamentWithEventGroup($eventGroupId){
		$tournaments = $this->model->where('event_group_id', $eventGroupId)->get();
		if(!$tournaments) return null;
		return $tournaments->toArray();
	}

	public function getTournamentById($tournamentId) {
		$tournament = $this->model->where('id', $tournamentId)->get();
		if(!$tournament) return null;
		return $tournament->toArray();
	}
} 