<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/07/2015
 * Time: 2:47 PM
 */

namespace TopBetta\Services\Tournaments;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\TournamentEventGroupRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\RaceResource;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Resources\Cache\CachedMeetingResourceService;
use TopBetta\Services\Resources\Cache\CachedRaceResourceService;
use TopBetta\Services\Resources\Cache\Sports\CachedCompetitionResourceService;
use TopBetta\Services\Resources\RaceResourceService;
use TopBetta\Services\Resources\SelectionResourceService;
use TopBetta\Services\Resources\Sports\MarketResourceService;
use TopBetta\Services\Resources\Tournaments\TournamentEventResourceService;
use TopBetta\Services\Sports\CompetitionService;
use TopBetta\Services\Sports\EventService;

class TournamentEventService
{


    /**
     * @var MeetingService
     */
    private $meetingService;
    /**
     * @var CompetitionService
     */
    private $competitionService;
    /**
     * @var TournamentEventResourceService
     */
    private $tournamentEventResourceService;
    /**
     * @var CachedMeetingResourceService
     */
    private $meetingResourceService;
    /**
     * @var CachedCompetitionResourceService
     */
    private $competitionResourceService;

    public function __construct(TournamentMeetingService $meetingService,
                                CompetitionService $competitionService,
                                TournamentEventGroupRepository $tournamentEventGroupRepository,
                                MarketResourceService $marketResourceService,
                                EventService $eventService,
                                SelectionResourceService $selectionResourceService,
                                RaceResource $raceResource,
                                CachedRaceResourceService $raceResourceService,
                                TournamentEventResourceService $tournamentEventResourceService,
                                CachedMeetingResourceService $meetingResourceService, CachedCompetitionResourceService $competitionResourceService
    )
    {
        $this->meetingService = $meetingService;
        $this->competitionService = $competitionService;
        $this->tournamentEventGroupRepository = $tournamentEventGroupRepository;
        $this->marketResourceService = $marketResourceService;
        $this->eventService = $eventService;
        $this->selectionResourceService = $selectionResourceService;
        $this->raceResource = $raceResource;
        $this->raceResourceService = $raceResourceService;
        $this->tournamentEventResourceService = $tournamentEventResourceService;
        $this->meetingResourceService = $meetingResourceService;
        $this->competitionResourceService = $competitionResourceService;
    }

//    public function getEventGroups($tournament, $eventId = null)
//    {
//        $data = array();
//
//        if( $tournament->type == 'racing') {
//            $eventGroup = $this->meetingService->getMeetingWithSelections($tournament->event_group_id, $eventId);
//            $selected = $eventGroup['selected_race'];
//            $eventGroup = $eventGroup['data'];
//
//        } else {
//            $eventGroup = $this->competitionService->getCompetitionsWithEvents(array('competition_id' => $tournament->event_group_id))['data']->first();
//            $selected = null;
//        }
//
//        return array('data' => array($eventGroup), 'selected_event' => $selected);
//    }

//    public function getEventGroups($tournament, $eventId = null)
//    {
//        $data = array();
//        $event_group_id = $tournament->event_group_id;
//
//        $event_group = $this->tournamentEventGroupRepository->getEventGroup($event_group_id);
//        $type = $event_group->type;
////        $type = $this->tournamentEventGroupRepository->getEventGroupType($event_group_id);
//
//        $events = $event_group->events()->get();
//
//        //set selected_event
//        $start_date = '';
//        $selected_event = '';
//
//        foreach ($events as $key => $event) {
//            if ($key == 0) {
//                $start_date = $event->start_date;
//                $selected_event = $event->id;
//            } else {
//                if ($event->start_date < $start_date) {
//                    $start_date = $event->start_date;
//                    $selected_event = $event->id;
//                }
//            }
//        }
//
//        $competitions = array();
//        $competitions_id = array();
//
//        foreach ($events as $key => $event) {
//            $competition = $event->competition()->first();
//            if (!in_array($competition->id, $competitions_id)) {
//                $competitions_id[] = $competition->id;
//                $competitions[] = $competition;
//            }
//        }
//        if ($type == 'sport') {
//
//            $competitions_resource = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($competitions), 'TopBetta\Resources\Sports\CompetitionResource');
//
//            $event_list = array();
//            foreach ($competitions_resource as $key => &$competition) {
//                $events_with_markets = $this->eventService->getEventsForCompetitionWithFilteredMarkets($competition);
//                $competition->setRelation('events', $events_with_markets);
//            }
//
//            return array('data' => $competitions_resource, 'selected_event' => $selected_event);
//        } else if ($type == 'race') {
//
//            $meetings_resource = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($competitions), 'TopBetta\Resources\MeetingResource');
//
//            foreach ($meetings_resource as $key => &$meeting) {
//
//                $races_resources = $this->raceResourceService->getRacesForMeeting($meeting->id);
//                $meeting->setRelation('races', $races_resources);
//
//                foreach($races_resources as &$race) {
////                    dd($race);
//                    $selections_resource = $this->selectionResourceService->getSelectionsForRace($race->id);
//
//                    $race->setRelation('selections', $selections_resource);
//                }
//            }
//
//            return array('data' => $meetings_resource, 'selected_event' => $selected_event);
//        }
//
//
//    }

    public function getEventGroups($tournament, $eventId = null)
    {
        $events = $this->tournamentEventResourceService->getEventResourceForTournament($tournament);

        $meetings = new Collection();
        $competitions = new Collection();

        //select the first event by default
        if (!$eventId) {
            $eventId = $this->getNextRacingEvent($events);
        }

        foreach ($events as $event) {
            if ($event->type == 'racing') {
                $meeting = $this->addOrGetRaceMeeting($meetings, $event);

                if ($eventId == $event->id) {
                    $race = $this->raceResourceService->getRaceWithSelections($event->id);
                } else {
                    $race = $this->raceResourceService->getRace($event->id, false);
                }

                $races = $meeting->races;
                $races->put($race->id, $race);
                $meeting->setRelation('races', $races);
                $meetings->put($meeting->id, $meeting);
            } else {
                $competition = $this->addorGetCompetition($competitions, $event);

                $events = $competition->events;
                $event = $this->eventService->getEventWithFilteredMarkets($event->id, $competition);
                $events->put($event->id, $event);
                $competition->setRelation('events', $events);
                $competitions->put($competition->id, $competition);
            }
        }

        foreach ($meetings as $meeting) {
            $this->meetingResourceService->loadTotesForMeeting($meeting);
        }

        return array("data" => array("meetings" => $meetings->values()->toArray(), "competitions" => $competitions->values()->toArray()), "selected_event" => $eventId);
    }

    public function getNextRacingEvent($events)
    {
        $racingEvents = $events->filter(function ($v) {
            return $v->type == 'racing';
        });

        if (!$racingEvents->count()) {
            return null;
        }

        $nextEvent = $events->filter(function ($v) {
            return $v->status == EventStatusRepositoryInterface::STATUS_SELLING;
        });

        if ($nextEvent->first()) {
            return $nextEvent->first()->id;
        }

        return $events->first()->id;
    }

    public function addOrGetRaceMeeting($meetings, $event)
    {
        if (!$meeting = $meetings->get($event->event_group_id)) {
            $meeting = $this->meetingResourceService->getMeeting($event->event_group_id);
            $meetings->put($meeting->id, $meeting);
        }

        return $meeting;
    }

    public function addOrGetCompetition($competitions, $event)
    {
        if (!$competition = $competitions->get($event->event_group_id)) {
            $competition = $this->competitionResourceService->getCompetitionResource($event->event_group_id);
            $competitions->put($competition->id, $competition);
        }

        return $competition;
    }

    public function getMeetings($tournament, $eventId = null)
    {
        $races = $tournament->getModel()->eventGroup->events
            ->load('competition');

        return $this->meetingService->getMeetingsByRaces($races, $eventId);
    }
}