<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 9/12/14
 * File creation time: 14:29
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentCompetitionRepositoryInterface;
use TopBetta\Models\TournamentCompetition;

class DbTournamentCompetiitonRepository extends BaseEloquentRepository implements TournamentCompetitionRepositoryInterface
{
    protected $model;

    public function __construct(TournamentCompetition $tournamentcompetition){
        $this->model = $tournamentcompetition;
    }

    public function getBySport($sportId)
    {
        return $this->model
            ->join('tbdb_event_group', 'tbdb_event_group.tournament_competition_id', '=', 'tbdb_tournament_competition.id')
            ->where('tournament_sport_id', $sportId)
            ->where('close_time', '>=', Carbon::now())
            ->groupBy('tbdb_tournament_competition.id')
            ->orderBy('tbdb_tournament_competition.name', 'ASC')
            ->get(array('tbdb_tournament_competition.*'));
    }

} 