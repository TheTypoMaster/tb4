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
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\DbMeetingVenueRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\MeetingResource;
use TopBetta\Resources\RaceResource;
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
                                BetService $betService,
                                DbMeetingVenueRepository $meetingRepository)
    {
        //set the meeting resource service to use
        $this->setMeetingResourceService();
        $this->raceResourceService = $raceResourceService;
        $this->selectionResourceService = $selectionResourceService;
        $this->resultService = $resultService;
        $this->betService = $betService;
        $this->meetingRepository = $meetingRepository;
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
            return array("data" => $meeting, "selected_race" => 0);
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

        $meetings = $this->getMeetingsForDate($selectedMeeting['data']->getStartDate());

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
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
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

    public function getMeetingsByRaces($races, $selected = null)
    {
        $meetings = new EloquentResourceCollection(new Collection, 'TopBetta\Resources\MeetingResource');

        $selectionsSet = false;

        foreach ($races as $race) {
            if (! $meeting = $meetings->get($race->competition->first()->id)) {
                $meeting = new MeetingResource($race->competition->first());
                $meeting->setRaces(new Collection);
                $meetings->put($meeting->id, $meeting);
            }

            $meeting->races()->push($resource = new RaceResource($race));

            $this->raceResourceService->loadTotesForRace($resource);

            if (($selected == $race->id) || (!$selectionsSet && $this->raceResourceService->isOpen($resource))) {
                $resource->loadRelation('selections');
                $selectionsSet = $race->id;
            }
        }

        if (!$selectionsSet) {
            $meetings->first()->races()->first()->loadRelation('selections');
        }

        return array("data" => $meetings, "selected_race" => $selectionsSet);
    }

    /**
     * get all meetings to array
     * @return mixed
     */
    public function getAllMeetings() {
        $meetings = $this->meetingRepository->findAll();
        return $meetings;
    }

}