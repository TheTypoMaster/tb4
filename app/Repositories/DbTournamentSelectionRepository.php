<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use TopBetta\Models\TournamentSelectionModel;

class DbTournamentSelectionRepository extends BaseEloquentRepository {

    protected $model;

    public function __construct(TournamentSelectionModel $tournamentselections){
        $this->model = $tournamentselections;
    }



} 