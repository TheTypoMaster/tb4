<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:43 AM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Cache\Sports\BaseCompetitionRepository;
use TopBetta\Repositories\Cache\Sports\CompetitionRepository;
use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\SportRepository;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRegionRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;

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
     * @var TournamentRepositoryInterface
     */
    private $tournaments;
    /**
     * @var TeamRepositoryInterface
     */
    private $teamProcessor;

    public function __construct(EventRepository $eventRepository,
                                CompetitionRepository $competitionRepository,
                                BaseCompetitionRepository $baseCompetitionRepository,
                                SportRepository $sportRepository,
                                CompetitionRegionRepositoryInterface $regionRepository,
                                TournamentCompetitionRepositoryInterface $tournamentCompetitionRepository,
                                TournamentRepositoryInterface $tournaments,
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
        $this->logprefix = 'SportsFeedService - GameListProcessor: ';
    }

    public function process($data)
    {
        //need to have external id to identify event
        if( ! $externalId = array_get($data, 'GameId', false)) {
            Log::error($this->logprefix."No GameId specified");
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

        //process base competition and competition
        $baseCompetition = null;
        $competition = null;
        if( $baseCompetition = array_get($data, 'CompetitionId', null) ) {
            $baseCompetition = $this->processBaseCompetition($baseCompetition, array_get($data, 'League', ''), array_get($sport, 'id', null), array_get($region, 'id', null));
            $competition = $this->processCompetition($data, array_get($baseCompetition, 'id', null), array_get($sport, 'id', null));
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
        Log::info($this->logprefix."Processing Sport: $sport");

        return $this->sportRepository->updateOrCreate(array("name" => $sport), "name");
    }

    private function processRegion($region)
    {
        Log::info($this->logprefix."Processing Region: $region");

        return $this->regionRepository->updateOrCreate(array("name" => $region), "name");
    }

    private function processBaseCompetition($baseCompetition, $competitionName, $sport, $region)
    {
        Log::info($this->logprefix. "Processing Base Competition: $baseCompetition");

        //competition name
        $data = array("name" => $competitionName, 'external_base_competition_id' => $baseCompetition);

        //set the sport
        if ( $sport ) {
            $data['sport_id'] = $sport;
        }

        //set the region
        if ( $region ) {
            $data['region_id'] = $region;
        }

        return $this->baseCompetitionRepository->updateOrCreate($data, "external_base_competition_id");
    }

    private function processCompetition($data, $baseCompetition, $sport)
    {
        $competitionName = array_get($data, 'League', '') . ' ' . array_get($data, 'Round', '');
        Log::info($this->logprefix. "Processing Competition: $competitionName");

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

        //make the external id for the event group
        $externalEventGroupId = 'C_' . $data['CompetitionId'];
        if($seasonId = array_get($data, 'SeasonId', null)) {
            $externalEventGroupId .= '_S_' . $seasonId;
        }
        if($roundId = array_get($data, 'RoundId', null)) {
            $externalEventGroupId .= '_R_' . $roundId;
        }

        //update start and close times
        $currentCloseTime = null;
        $eventDate = array_get($data, 'EventTime', null);
        if( $competition = $this->competitionRepository->getCompetitionByExternalId($externalEventGroupId) ) {
            if ($eventDate && $competition['start_date'] > $eventDate) {
                $compData['start_date'] = $eventDate;
            }
            if ($eventDate && $competition['close_time'] < $eventDate) {
                $currentCloseTime       = $competition['close_time'];
                $compData['close_time'] = $eventDate;
            }
            //update competition
            $this->competitionRepository->updateWithId($competition['id'], $compData);
        } else if ( array_get($data, 'Type', null) == 'update' && ($competition = $this->competitionRepository->findByName($competitionName)) ) {
            if ($eventDate && $competition['start_date'] > $eventDate) {
                $compData['start_date'] = $eventDate;
            }
            if ($eventDate && $competition['close_time'] < $eventDate) {
                $currentCloseTime       = $competition['close_time'];
                $compData['close_time'] = $eventDate;
            }
            //update competition
            $this->competitionRepository->updateWithId($competition['id'], $compData);
        } else {
            $compData['start_date'] = $eventDate;
            $compData['close_time'] = $eventDate;
            $compData['external_event_group_id'] = $externalEventGroupId;
            //create the comp
            $competition = $this->competitionRepository->create($compData);
        }

        //update tournaments
        if( $currentCloseTime && $eventDate && $currentCloseTime < $eventDate ) {
            $this->tournaments->updateTournamentByEventGroupId($competition['id'], $competition['close_time']);
        }

        return $competition;
    }

    private function processEvent($externalId, $name, $time, $competition)
    {
        Log::info($this->logprefix. "Processing Event: $name");

        $data = array("name" => $name, "external_event_id" => $externalId, "event_status_id" => 1);

        //set the start date
        if( $time ) {
            $data['start_date'] = $time;
        }

        //create the event
        $event = $this->eventRepository->updateOrCreate($data, "external_event_id");

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