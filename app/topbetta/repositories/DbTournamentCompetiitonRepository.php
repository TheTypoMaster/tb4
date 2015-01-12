<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 9/12/14
 * File creation time: 14:29
 * Project: tb4
 */

use TopBetta\TournamentCompetition;

class DbTournamentCompetiitonRepository extends BaseEloquentRepository {
    protected $model;

    public function __construct(TournamentCompetition $tournamentcompetition){
        $this->model = $tournamentcompetition;
    }

} 