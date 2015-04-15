<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:43 AM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRegionRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentCompetitionRepositoryInterface;
use TopBetta\Repositories\DbTournamentRepository;

class GameListProcessor extends AbstractFeedProcessor {

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var BaseCompetitionRepositoryInterface
     */
    private $baseCompetitionRepository;
    /**
     * @var SportRepositoryInterface
     */
    private $sportRepository;
    /**
     * @var CompetitionRegionRepositoryInterface
     */
    private $regionRepository;
    /**
     * @var TournamentCompetitionRepositoryInterface
     */
    private $tournamentCompetitionRepository;
    /**
     * @var DbTournamentRepository
     */
    private $tournaments;
    /**
     * @var TeamRepositoryInterface
     */
    private $teamProcessor;

    public function __construct(EventRepositoryInterface $eventRepository,
                                CompetitionRepositoryInterface $competitionRepository,
                                BaseCompetitionRepositoryInterface $baseCompetitionRepository,
                                SportRepositoryInterface $sportRepository,
                                CompetitionRegionRepositoryInterface $regionRepository,
                                TournamentCompetitionRepositoryInterface $tournamentCompetitionRepository,
                                DbTournamentRepository $tournaments,
                                TeamProcessor $teamProcessor)
    {
        $this->eventRepository = $eventRepository;
        $this->competitionRepository = $competitionRepository;
        $this->baseCompetitionRepository = $baseCompetitionRepository;
        $this->sportRepository = $sportRepository;
        $this->regionRepository = $regionRepository;
        $this->tournamentCompetitionRepository = $tournamentCompetitionRepository;
        $this->tournaments = $tournaments;
        $this->teamProcessor = $teamProcessor;
    }

    public function process($data)
    {
        //need to have external id to identify event
        if( ! $externalId = array_get($data, 'GameId', false)) {
            return 0;
        }

        //process sport
        if( $sport = array_get($data, 'Sport', null) ) {
            $sport = $this->processSport($sport);
        }

        //process region
        if( $region = array_get($data, 'Region', null) ) {
            $region = $this->processRegion($region);
        }

        //process base competition
        if( $baseCompetition = array_get($data, 'League', null) ) {
            $baseCompetition = $this->processBaseCompetition($baseCompetition, array_get($sport, 'id', null), array_get($region, 'id', null));
        }

        //process tournament competition and event group
        $competition = null;
        if( $league = array_get($data, 'League', null) ) {
            $competition = $this->processCompetition($league, array_get($data, 'Round', ''), array_get($data, 'EventTime', null), $externalId, array_get($baseCompetition, 'id', null), array_get($sport, 'id', null));
        }

        //process event
        $event = $this->processEvent($externalId, array_get($data, 'EventName', null), array_get($data, 'EventTime'), $competition);

        //process teams
        $teams = $this->teamProcessor->processArray(array_get($data, 'Teams', array()), $event['id']);
        if( $event ) {
            $this->processEventTeams($event['id'], $teams, array_get($data, 'Teams', array()));
        }

    }

    private function processSport($sport)
    {
        Log::info("BackAPI: Sports - Processing Sport: $sport");

        return $this->sportRepository->updateOrCreate(array("name" => $sport), "name");
    }

    private function processRegion($region)
    {
        Log::info("BackAPI: Sports - Processing Region: $region");

        return $this->regionRepository->updateOrCreate(array("name" => $region), "name");
    }

    private function processBaseCompetition($baseCompetition, $sport, $region)
    {
        Log::info("BackAPI: Sports - Processing Base Competition: $baseCompetition");

        //competition name
        $data = array("name" => $baseCompetition);

        //set the sport
        if ( $sport ) {
            $data['sport_id'] = $sport;
        }

        //set the region
        if ( $region ) {
            $data['region_id'] = $region;
        }

        return $this->baseCompetitionRepository->updateOrCreate($data, "name");
    }

    private function processCompetition($league, $round, $eventDate, $externalId, $baseCompetition, $sport)
    {
        $competitionName = $league . ' ' . $round;
        Log::info("BackAPI: Sports - Processing Competition: $competitionName");

        $tournCompData = array("name" => $competitionName);
        $compData = $tournCompData;

        //set the base competition
        if( $baseCompetition ) {
            $compData['base_competition_id'] = $baseCompetition;
        }

        //set the sport
        if( $sport ) {
            $compData['sport_id'] = $sport;
            $tournCompData['tournament_sport_id'] = $sport;
        }

        //create the tournament comp and attach to competition
        $tournComp = $this->tournamentCompetitionRepository->updateOrCreate($tournCompData, 'name');
        if ( $tournCompId = array_get($tournComp, 'id', null) ) {
            $compData['tournament_competition_id'] = $tournCompId;
        }

        //update start and close times
        if( $competition = $this->competitionRepository->find($competitionName) ) {
            if( $eventDate && $competition['start_date'] > $eventDate ) { $compData['start_date'] = $eventDate; }
            if( $eventDate && $competition['close_time'] < $eventDate ) { $compData['close_time'] = $eventDate; }
        } else {
            $compData['start_date'] = $eventDate;
            $compData['close_time'] = $eventDate;
            $compData['external_event_group_id'] = $externalId;
        }

        //create/update the competition
        $competition = $this->competitionRepository->updateOrCreate($compData, "name");

        //TODO: Is it necessary to update tournaments?

        return $competition;
    }

    private function processEvent($externalId, $name, $time, $competition)
    {
        Log::info("BackAPI: Sports - Processing Event: $name");

        $data = array("name" => $name, "external_event_id" => $externalId, "event_status_id" => 1);

        //set the start date
        if( $time ) {
            $data['start_date'] = $time;
        }

        //create the event
        $event = $this->eventRepository->updateOrCreate($data, "name");

        if( array_get($event, 'id', null) && $competition ) {
            $this->eventRepository->addToCompetition($event['id'], $competition['id']);
        }

        return $event;
    }

    private function processEventTeams($event, $teams, $teamData)
    {
        $teamPositions = array_map(function($team) {
            return array("team_position" => $team['team_position']);
        }, $teamData);

        return $this->eventRepository->addTeams($event, array_except(array_combine($teams, $teamPositions), 0));
    }




}