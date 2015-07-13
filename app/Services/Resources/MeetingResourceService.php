<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 10:35 AM
 */

namespace TopBetta\Services\Resources;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\MeetingResource;

class MeetingResourceService {

    private $meetingResource = 'TopBetta\Resources\MeetingResource';
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var RaceResourceService
     */
    private $raceService;
    /**
     * @var SelectionResourceService
     */
    private $selectionService;

    public function __construct(CompetitionRepositoryInterface $competitionRepository, RaceResourceService $raceService, SelectionResourceService $selectionService)
    {
        $this->competitionRepository = $competitionRepository;
        $this->raceService = $raceService;
        $this->selectionService = $selectionService;
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false)
    {

        $collection = $this->competitionRepository->getRacingCompetitionsByDate(
            $date,
            $type,
            $withRaces
        );

        return new EloquentResourceCollection($collection, $this->meetingResource);
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

        return $model;
    }

}