<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/07/2015
 * Time: 1:27 PM
 */

namespace TopBetta\Services\Racing;

use App;
use Carbon\Carbon;
use TopBetta\Services\Betting\BetService;
use TopBetta\Services\Resources\Cache\CachedMeetingResourceService;
use TopBetta\Services\Resources\Cache\CachedSelectionResourceService;
use TopBetta\Services\Resources\MeetingResourceService;
use TopBetta\Services\Resources\RaceResourceService;
use TopBetta\Services\Resources\SelectionResourceService;

class MeetingService {

    /**
     * @var CachedMeetingResourceService
     */
    protected $meetingResourceService;
    /**
     * @var RaceResourceService
     */
    private $raceResourceService;
    /**
     * @var SelectionResourceService
     */
    private $selectionResourceService;
    /**
     * @var RaceResultService
     */
    private $resultService;
    /**
     * @var BetService
     */
    private $betService;

    public function __construct(RaceResourceService $raceResourceService,
                                CachedSelectionResourceService $selectionResourceService,
                                RaceResultService $resultService,
                                BetService $betService)
    {
        //set the meeting resource service to use
        $this->setMeetingResourceService();
        $this->raceResourceService = $raceResourceService;
        $this->selectionResourceService = $selectionResourceService;
        $this->resultService = $resultService;
        $this->betService = $betService;
    }

    public function getSmallMeetingsWithRaces($date = null)
    {
        $date = $date ? Carbon::createFromFormat('Y-m-d', $date) : Carbon::now();

        return $this->meetingResourceService->getSmallMeetings($date);
    }

    public function getMeetingWithSelections($id, $raceId = null)
    {
        $meeting = $this->meetingResourceService->getMeeting($id, true);

        if( ! $meeting->races->count() ) {
            return $meeting;
        }

        $meeting->races->setRelations(
            'bets',
            'event_id',
            $this->betService->getBetsByEventGroupForAuthUser($id)
        );

        foreach( $meeting->races as $event ) {

            if ( ($raceId && $event->id == $raceId) || ( ! $raceId && $this->raceResourceService->isOpen($event)) ) {

                $event->setSelections($this->selectionResourceService->getSelectionsForRace($event->id));

                return array("data" => $meeting, "selected_race" => $event->id);
            }

        }

        $meeting->races->first()->setSelections($this->selectionResourceService->getSelectionsForRace($meeting->races->first()->id));

        return array("data" => $meeting, "selected_race" => $meeting->races->first()->id);
    }

    public function getMeetingsWithSelectionForMeeting($meetingId, $raceId = null)
    {
        $selectedMeeting = $this->getMeetingWithSelections($meetingId, $raceId);

        $meetings = $this->getMeetingsForDate($selectedMeeting['data']->getStartDate()->toDateString());

        foreach( $meetings as $meeting ) {
            if( $meeting->id == $selectedMeeting['data']->id ) {
                $meeting->setRaces($selectedMeeting['data']->races);

                break;
            }
        }

        return array( "data" => $meetings, "selected_race" => $selectedMeeting['selected_race']);
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false, $withRunners = false)
    {
        if( ! $date ) {
            $date = Carbon::now();
        } else {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }

        $collection = $this->meetingResourceService->getMeetingsForDate($date, $type, $withRaces);

        if( $withRaces ) {
            foreach($collection as $meeting) {

                $meeting->races->setRelations(
                    'bets',
                    'event_id',
                    $this->betService->getBetsByEventGroupForAuthUser($meeting->id)
                );
            }
        }

        return $collection;
    }

    public function getMeeting($id, $withRaces = false)
    {
        $meeting = $this->meetingResourceService->getMeeting($id, $withRaces);

        if( $withRaces ) {
            $meeting->races->setRelations('bets', 'event_id', $this->betService->getBetsByEventGroupForAuthUser($meeting->id));
        }

        return $meeting;

    }

    /**
     * Injects the meeting resource service so we can override in inheritors if neccesary
     * @return $this
     */
    public function setMeetingResourceService()
    {
        $this->meetingResourceService = App::make('TopBetta\Services\Resources\Cache\CachedMeetingResourceService');
        return $this;
    }

}