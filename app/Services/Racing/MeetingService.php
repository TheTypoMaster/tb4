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

class MeetingService extends RacingResourceService {


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

        return $this->competitionRepository->getRacingCompetitionsByDate(
            $date,
            $type,
            $withRaces
        );
    }

    public function getMeeting($id, $withRaces = false)
    {
        $model = $this->competitionRepository->find($id);

        if( ! $model ) {
            throw new ModelNotFoundException;
        }

        if( $withRaces ) {
            $model->load('competitionEvents');
        }

        return $model;
    }

    public function getMeetingWithSelections($id, $raceId = null)
    {
        $meeting = $this->getMeeting($id, true);

        foreach( $meeting->competitionEvents as $event ) {

            if ( ($raceId && $event->id == $raceId) || ( ! $raceId && $this->raceService->isOpen($event)) ) {
                $event->load(array('markets.selections') + array_map(function($q) {
                        return 'markets.selections.'.$q;
                    }, $this->selectionService->getDefaultRelations()));

                return $meeting;
            }
        }

        $meeting->competionsEvents->first()->load(array('market.selections') + array_map(function($q) {
                return 'market.selections.'.$q;
            }, $this->selectionService->getDefaultRelations()));

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