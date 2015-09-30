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
        if( ! $externalId = array_get($data, 'event_id', false)) {
            Log::error($this->logprefix."No event_id specified");
            return 0;
        }

        //process sport
        if( ($sportName = array_get($data, 'sport_name', null)) && ! ($sport = $this->modelContainer->getSport($sportName))) {
            $sport = $this->processSport($sportName);
            $this->modelContainer->addSport($sport, $sportName);
        }

        //process region
        if( ($regionName = array_get($data, 'region_name', null)) && ! ($region = $this->modelContainer->getRegion($regionName)) ) {
            $region = $this->processRegion($regionName);
            $this->modelContainer->addRegion($region, $regionName);
        }

        //process base competition and competition
        $baseCompetition = null;
        $competition = null;
        if( $baseCompetition = array_get($data, 'league_id', null) ) {
            $baseCompetition = $this->processBaseCompetition($baseCompetition, array_get($data, 'CompetitionId'), array_get($data, 'league_name', ''), $sport, array_get($region, 'id', null));
            $competition = $this->processCompetition($data, $baseCompetition, $sport);
        }

        //process event
        $event = $this->processEvent($externalId, array_get($data, 'EventId'), array_get($data, 'event_name', null), array_get($data, 'event_start_time'), $competition);

        //process teams
        $this->teamProcessor->setModelContainer($this->modelContainer)->setEvent($event);
        $teams = $this->teamProcessor->processArray(array_get($data, 'Teams', array()));
        if( $event ) {
            $this->processEventTeams($event, $teams, array_get($data, 'Teams', array()));
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

        return $this->regionRepository->updateOrCreateAndReturnModel(array("name" => $region), "name");
    }

    private function processBaseCompetition($baseCompetition, $baseCompetitionBg, $competitionName, $sport, $region)
    {
        Log::info($this->logprefix. "Processing Base Competition: $baseCompetition");

        //competition name
        $data = array("name" => $competitionName, 'serena_base_competition_id' => $baseCompetition);

        //set the sport
        if ( $sport ) {
            $data['sport_id'] = $sport->id;
        }

        //set the region
        if ( $region ) {
            $data['region_id'] = $region;
        }

        if ($baseCompetitionModel = $this->modelContainer->getBaseCompetition($baseCompetition)) {
            $baseCompetitionModel = $this->baseCompetitionRepository->update($baseCompetitionModel, $data);
        } else if ($baseCompetitionModel = $this->baseCompetitionRepository->getBySerenaId($baseCompetition)) {
            $baseCompetitionModel = $this->baseCompetitionRepository->update($baseCompetitionModel, $data);
        } else if ($baseCompetitionBg && ($baseCompetitionModel = $this->baseCompetitionRepository->getByExternalId($baseCompetitionBg))) {
            $baseCompetitionModel = $this->baseCompetitionRepository->update($baseCompetitionModel, $data);
        } else {
            $baseCompetitionModel = $this->baseCompetitionRepository->create($data);
        }

        $baseCompetitionModel->sport = $sport;
        $this->modelContainer->addBaseCompetition($baseCompetitionModel, $baseCompetition);

        return $baseCompetitionModel;
    }

    private function processCompetition($data, $baseCompetition, $sport)
    {
        $competitionName = array_get($data, 'league_name', '') . ' ' . array_get($data, 'round_name', '');
        Log::info($this->logprefix. "Processing Competition: $competitionName");

        $tournCompData = array("name" => $competitionName);
        $compData = $tournCompData;

        //set the base competition
        if( $baseCompetition ) {
            $compData['base_competition_id'] = $baseCompetition->id;
        }

        //set the sport
        if( $sport ) {
            $compData['sport_id'] = $sport->id;
            $tournCompData['tournament_sport_id'] = $sport->id;
        }

        //create the tournament comp and attach to competition
        $tournComp = $this->tournamentCompetitionRepository->updateOrCreate($tournCompData, 'name');
        if ( $tournCompId = array_get($tournComp, 'id', null) ) {
            $compData['tournament_competition_id'] = $tournCompId;
        }

        $externalEventGroupId = $this->makeCompositeCompetitionId('CompetitionId', 'SeasonId', 'RoundId', $data);
        $serenaEventGroupId = $this->makeCompositeCompetitionId('league_id', 'season_id', 'round_id', $data);


        //update start and close times
        $currentCloseTime = null;
        $eventDate = array_get($data, 'event_start_time', null);


        if( ($competition = $this->modelContainer->getCompetition($serenaEventGroupId)) || ($competition = $this->competitionRepository->getBySerenaId($serenaEventGroupId)) ||($competition = $this->competitionRepository->getCompetitionByExternalId($externalEventGroupId)) ) {
            if ($eventDate && $competition['start_date'] > $eventDate) {
                $compData['start_date'] = $eventDate;
            }
            if ($eventDate && $competition['close_time'] < $eventDate) {
                $currentCloseTime       = $competition['close_time'];
                $compData['close_time'] = $eventDate;
            }

            $compData['serena_event_group_id'] = $serenaEventGroupId;

            //update competition
            $competition = $this->competitionRepository->update($competition, $compData);
            $competition->baseCompetition = $baseCompetition;
            $this->modelContainer->addCompetition($competition, $serenaEventGroupId);
        } else if ( array_get($data, 'Type', null) == 'update' && ($competition = $this->competitionRepository->findByName($competitionName)) ) {
            if ($eventDate && $competition['start_date'] > $eventDate) {
                $compData['start_date'] = $eventDate;
            }
            if ($eventDate && $competition['close_time'] < $eventDate) {
                $currentCloseTime       = $competition['close_time'];
                $compData['close_time'] = $eventDate;
            }

            $compData['serena_event_group_id'] = $serenaEventGroupId;

            //update competition
            $competition = $this->competitionRepository->update($competition, $compData);
            $competition->baseCompetition = $baseCompetition;
            $this->modelContainer->addCompetition($competition, $serenaEventGroupId);
        } else {
            $compData['start_date'] = $eventDate;
            $compData['close_time'] = $eventDate;
            $compData['serena_event_group_id'] = $serenaEventGroupId;
            //create the comp
            $competition = $this->competitionRepository->create($compData);
            $competition->baseCompetition = $baseCompetition;
            $this->modelContainer->addCompetition($competition, $serenaEventGroupId);
        }

        //update tournaments
        if( $currentCloseTime && $eventDate && $currentCloseTime < $eventDate ) {
            $this->tournaments->updateTournamentByEventGroupId($competition['id'], $competition['close_time']);
        }

        return $competition;
    }

    private function makeCompositeCompetitionId($competitionId, $seasonId, $roundId, $data)
    {
        //make the external id for the event group
        $externalEventGroupId = 'C_' . $data[$competitionId];
        if($extSeasonId = array_get($data, $seasonId, null)) {
            $externalEventGroupId .= '_S_' . $extSeasonId;
        }
        if($extRoundId = array_get($data, $roundId, null)) {
            $externalEventGroupId .= '_R_' . $extRoundId;
        }


        return $externalEventGroupId;
    }

    private function processEvent($externalId, $externalIdBg, $name, $time, $competition)
    {
        Log::info($this->logprefix. "Processing Event: $name");

        $data = array("name" => $name, "serena_event_id" => $externalId, "event_status_id" => 1);

        //set the start date
        if( $time ) {
            $data['start_date'] = $time;
        }

        //create the event
        if ($event = $this->modelContainer->getEvent($externalId)) {
            $event = $this->eventRepository->update($event, $data);
        } else if ($event = $this->eventRepository->getBySerenaId($externalId)) {
            $event = $this->eventRepository->update($event, $data);
        } else if ($event = $this->eventRepository->getEventModelFromExternalId($externalIdBg)) {
            $event = $this->eventRepository->update($event, $data);
        } else {
            $event = $this->eventRepository->create($data);
        }

        $this->modelContainer->addEvent($event, $event->external_event_id);

        if( array_get($event, 'id', null) && $competition ) {
            $this->eventRepository->addModelToCompetition($event, $competition);
        }

        return $event;
    }

    private function processEventTeams($event, $teams, $teamData)
    {
        $teamPositions = array_map(function($team) {
            return array("team_position" => $team['team_position']);
        }, $teamData);

        return $this->eventRepository->addTeamsToModel($event, array_except(array_combine($teams, $teamPositions), 0));
    }




}