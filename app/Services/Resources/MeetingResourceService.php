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
use TopBetta\Repositories\Contracts\ProductProviderMatchRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\MeetingResource;
use TopBetta\Services\Products\ProductService;
use TopBetta\Services\Racing\RaceResultService;

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
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(CompetitionRepositoryInterface $competitionRepository, RaceResourceService $raceService, SelectionResourceService $selectionService, ProductService $productService, RaceResultService $resultService)
    {
        $this->competitionRepository = $competitionRepository;
        $this->raceService = $raceService;
        $this->selectionService = $selectionService;
        $this->productService = $productService;
        $this->resultService = $resultService;
    }

    public function getSmallMeetings($date, $type = null, $withRaces = false)
    {
        $collection = $this->competitionRepository->getRacingCompetitionsByDate(
            $date,
            $type,
            $withRaces
        );

        $meetings = new EloquentResourceCollection($collection, 'TopBetta\Resources\SmallMeetingResource');

        if ($withRaces) {
            foreach ($meetings as $meeting) {
                $this->resultService->loadResultsForRaces($meeting->races);
            }
        }

        return $meetings;
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false)
    {

        $collection = $this->competitionRepository->getRacingCompetitionsByDate(
            $date,
            $type,
            true
        );

        $meetings = new EloquentResourceCollection($collection, $this->meetingResource);
        foreach ($meetings as $meeting) {
            if ($withRaces) {
                $this->resultService->loadResultsForRaces($meeting->races);
                $this->loadTotesForMeeting($meeting);
            } else {
                $meeting->without('races');
            }
        }

        return $meetings;
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

        if ($withRaces) {
            $this->resultService->loadResultsForRaces($model->races);
            $this->loadTotesForMeeting($model);
        }

        return $model;
    }

    public function loadTotesForMeeting(MeetingResource $meeting)
    {
        $products = $this->productService->getAuthUserProductsForCompetition($meeting->getModel());

        $products = new EloquentResourceCollection($products, 'TopBetta\Resources\ProductResource');

        foreach ($meeting->races as $race) {

            $race->setProducts($products);
        }

        return $meeting;
    }

}