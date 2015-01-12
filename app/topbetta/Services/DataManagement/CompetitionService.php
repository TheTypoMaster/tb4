<?php namespace TopBetta\Services\DataManagement;
/**
 * Coded by Oliver Shanahan
 * File creation date: 9/12/14
 * File creation time: 14:16
 * Project: tb4
 */

use TopBetta\Repositories\DbTournamentCompetiitonRepository;
use TopBetta\Repositories\DbCompetitionRepository;
use TopBetta\Repositories\DbTournamentRepository;

class CompetitionService {

    protected $tournamentcompetitions;
    protected $competitions;
    protected $tournaments;

    public function __construct(DbTournamentCompetiitonRepository $tournamentcompetitions,
                                DbCompetitionRepository $competitions,
                                DbTournamentRepository $tournaments){
        $this->tournamentcompetitions = $tournamentcompetitions;
        $this->competitions = $competitions;
        $this->tournaments = $tournaments;
    }

    public function createCompetition($data){

        // validate the data


        // create tournament competiton record
        $tc = array('tournament_sport_id' => $data['sport_id'], 'name' => $data['competition_name'], 'status_flag' => '1');
        $tcModel = $this->tournamentcompetitions->updateOrCreate($tc);

        // create competition/event group record
        $eg = array('wagering_api_id' => 1, 'name' => $data['competition_name'], 'tournament_competition_id' => $tcModel['id'], 'start_date' => $data['start_date'],
                        'display_flag' => 1, 'sport_id' => $data['sport_id'], 'close_time' => $data['start_date']);
        $egModel = $this->competitions->updateOrCreate($eg);

        // create tournament record



    }
} 