<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/07/2015
 * Time: 2:47 PM
 */

namespace TopBetta\Services\Tournaments;


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
        $data = array();

        if( ! $tournament->eventGroup()->first()->sport || $tournament->eventGroup()->first()->sport->isRacing() ) {
            $eventGroup = $this->meetingService->getMeetingWithSelections($tournament->event_group_id, $eventId);
            $data['selected_race'] = $eventGroup['selected_race'];
            $eventGroup = $eventGroup['data'];
        } else {
            $eventGroup = $this->competitionService->getCompetitionsWithEvents(array('competition_id' => $tournament->event_group_id))['data']->first();
            $selected = null;
        }

        $data['data'] = array($eventGroup);

        return $data;
    }
}