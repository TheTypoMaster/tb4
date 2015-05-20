<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 9/12/14
 * File creation time: 14:29
 * Project: tb4
 */

use TopBetta\Repositories\Contracts\TournamentCompetitionRepositoryInterface;
use TopBetta\TournamentCompetition;

class DbTournamentCompetiitonRepository extends BaseEloquentRepository implements TournamentCompetitionRepositoryInterface
{
    protected $model;

    public function __construct(TournamentCompetition $tournamentcompetition){
        $this->model = $tournamentcompetition;
    }

    public function getBySport($sportId)
    {
        return $this->model->where('tournament_sport_id', $sportId)->orderBy('name', 'ASC')->get();
    }

} 