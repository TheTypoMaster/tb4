<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/07/2015
 * Time: 2:47 PM
 */

namespace TopBetta\Services\Tournaments;


use Illuminate\Support\Collection;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Resources\MeetingResourceService;
use TopBetta\Services\Resources\Sports\CompetitionResourceService;
use TopBetta\Services\Resources\Tournaments\TournamentResourceService;
use TopBetta\Services\Sports\CompetitionService;

class TournamentEventService {


    /**
     * @var MeetingService
     */
    private $meetingService;
    /**
     * @var CompetitionService
     */
    private $competitionService;

    public function __construct(MeetingService $meetingService, CompetitionService $competitionService)
    {
        $this->meetingService = $meetingService;
        $this->competitionService = $competitionService;
    }

    public function getEventGroups($tournament, $eventId = null)
    {
        if( ! ($sport = $tournament->eventGroup->events->competition->first()->sport) || $sport->isRacing() ) {
            return $this->getMeetings($tournament, $eventId);
        } else {
            $eventGroup = $this->competitionService->getCompetitionsWithEvents(array('competition_id' => $tournament->event_group_id))['data']->first();
            $selected = null;
        }

        return array('data' => array($eventGroup), 'selected_event' => $selected);
    }

    public function getMeetings($tournament, $eventId = null)
    {
        $races = $tournament->eventGroup->events
            ->load('competition');

        return $this->meetingService->getMeetingsByRaces($races, $eventId);
    }
}