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
use TopBetta\Repositories\TournamentEventGroupRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\RaceResource;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Resources\RaceResourceService;
use TopBetta\Services\Resources\SelectionResourceService;
use TopBetta\Services\Resources\Sports\MarketResourceService;
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

    public function __construct(TournamentMeetingService $meetingService, CompetitionService $competitionService,
                                TournamentEventGroupRepository $tournamentEventGroupRepository,
                                MarketResourceService $marketResourceService,
                                EventService $eventService,
                                SelectionResourceService $selectionResourceService,
                                RaceResource $raceResource,
                                RaceResourceService $raceResourceService
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

    public function getEventGroups($tournament, $eventId = null)
    {
        $data = array();
        $event_group_id = $tournament->event_group_id;

        $event_group = $this->tournamentEventGroupRepository->getEventGroup($event_group_id);
        $type = $event_group->type;
//        $type = $this->tournamentEventGroupRepository->getEventGroupType($event_group_id);

        $events = $event_group->events()->get();

        //set selected_event
        $start_date = '';
        $selected_event = '';

        foreach ($events as $key => $event) {
            if ($key == 0) {
                $start_date = $event->start_date;
                $selected_event = $event->id;
            } else {
                if ($event->start_date < $start_date) {
                    $start_date = $event->start_date;
                    $selected_event = $event->id;
                }
            }
        }

        $competitions = array();
        $competitions_id = array();

        foreach ($events as $key => $event) {
            $competition = $event->competition()->first();
            if (!in_array($competition->id, $competitions_id)) {
                $competitions_id[] = $competition->id;
                $competitions[] = $competition;
            }
        }
        if ($type == 'sport') {

            $competitions_resource = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($competitions), 'TopBetta\Resources\Sports\CompetitionResource');

            $event_list = array();
            foreach ($competitions_resource as $key => &$competition) {
                $events_with_markets = $this->eventService->getEventsForCompetitionWithFilteredMarkets($competition);
                $competition->setRelation('events', $events_with_markets);
            }

            return array('data' => $competitions_resource, 'selected_event' => $selected_event);
        } else if ($type == 'race') {

            $meetings_resource = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($competitions), 'TopBetta\Resources\MeetingResource');

            foreach ($meetings_resource as $key => &$meeting) {

                $races_resources = $this->raceResourceService->getRacesForMeeting($meeting->id);
                $meeting->setRelation('races', $races_resources);

                foreach($races_resources as &$race) {
                    $selections_resource = $this->selectionResourceService->getSelectionsForRace($race->id);

                    $race->setRelation('selections', $selections_resource);
                }
            }

            return array('data' => $meetings_resource, 'selected_event' => $selected_event);
        }


    }


    public function getMeetings($tournament, $eventId = null)
    {
        $races = $tournament->getModel()->eventGroup->events
            ->load('competition');

        return $this->meetingService->getMeetingsByRaces($races, $eventId);
    }
}