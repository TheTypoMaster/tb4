<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 10:35 AM
 */

namespace TopBetta\Services\Racing;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\MeetingResource;

class MeetingService extends RacingResourceService {

    private $meetingResource = 'TopBetta\Resources\MeetingResource';
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var RaceService
     */
    private $raceService;
    /**
     * @var SelectionService
     */
    private $selectionService;

    public function __construct(CompetitionRepositoryInterface $competitionRepository, RaceService $raceService, SelectionService $selectionService)
    {
        $this->competitionRepository = $competitionRepository;
        $this->raceService = $raceService;
        $this->selectionService = $selectionService;
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false, $withRunners = false)
    {
        if( ! $date ) {
            $date = Carbon::now();
        } else {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }

        $collection = $this->competitionRepository->getRacingCompetitionsByDate(
            $date,
            $type,
            $withRaces
        );

        $collection = new EloquentResourceCollection($collection, $this->meetingResource);

        if( $withRaces ) {
            foreach($collection as $meeting) {
                $this->raceService->loadResultsForRaces($meeting->races);
            }
        }

        return $collection;
    }

    public function getMeetingModel($id, $withRaces = false)
    {
        $model = $this->competitionRepository->find($id);

        if( ! $model ) {
            throw new ModelNotFoundException;
        }

        if( $withRaces ) {
            $model->load(array('competitionEvents', 'competitionEvents.eventstatus'));
        }

        return $model;
    }

    public function getMeeting($id, $withRaces = false)
    {
        $model = $this->getMeetingModel($id, $withRaces);

        $model = new MeetingResource($model);

        if( $withRaces ) {
            $this->raceService->loadResultsForRaces($model->races);
        }

        return $model;
    }

    public function getMeetingsWithSelectionForMeeting($meetingId, $raceId = null)
    {
        $selectedMeeting = $this->getMeetingWithSelections($meetingId, $raceId);

        $meetings = $this->getMeetingsForDate($selectedMeeting->getStartDate()->toDateString());

        foreach( $meetings as $meeting ) {
            if( $meeting->id == $selectedMeeting->id ) {
                $meeting->setRaces($selectedMeeting->races);
                break;
            }
        }

        return $meetings;
    }

    public function getMeetingWithSelections($id, $raceId = null)
    {
        $meeting = $this->getMeetingModel($id, true);

        if( ! $meeting->competitionEvents->count() ) {
            return new MeetingResource($meeting);
        }

        foreach( $meeting->competitionEvents as $event ) {

            if ( ($raceId && $event->id == $raceId) || ( ! $raceId && $this->raceService->isOpen($event)) ) {
                $event->load(array('markets.selections') + array_map(function($q) {
                        return 'markets.selections.'.$q;
                    }, $this->selectionService->getDefaultRelations()));

                $meeting = new MeetingResource($meeting);
                $this->raceService->loadResultsForRaces($meeting->races);

                return $meeting;
            }
        }

        $meeting->competionsEvents->first()->load(array('market.selections') + array_map(function($q) {
                return 'market.selections.'.$q;
            }, $this->selectionService->getDefaultRelations()));

        $meeting = new MeetingResource($meeting);
        $this->raceService->loadResultsForRaces($meeting->races);

        return $meeting;
    }

    public function formatForResponse($meeting)
    {
        $response = array(
            "id" => $meeting->id,
            "name" => $meeting->name,
            "description" => $meeting->description,
            "state" => $meeting->state,
            "track" => $meeting->track,
            "weather" => $meeting->weather,
            "type" => $meeting->type_code,
            "start_date" => $meeting->start_date,
            "country" => $meeting->coutnry,
            "grade" => $meeting->meeting_grade,
            "rail_position" => $meeting->rail_position
        );

        if( isset($meeting->competitionEvents) ) {
            $response['races'] = $this->raceService->formatCollectionsForResponse($meeting->competitionEvents);
        }

        return $response;
    }


}