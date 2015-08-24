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
     * @var ProductProviderMatchRepositoryInterface
     */
    private $productProviderMatchRepository;

    public function __construct(CompetitionRepositoryInterface $competitionRepository, RaceResourceService $raceService, SelectionResourceService $selectionService, ProductProviderMatchRepositoryInterface $productProviderMatchRepository)
    {
        $this->competitionRepository = $competitionRepository;
        $this->raceService = $raceService;
        $this->selectionService = $selectionService;
        $this->productProviderMatchRepository = $productProviderMatchRepository;
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false)
    {

        $collection = $this->competitionRepository->getRacingCompetitionsByDate(
            $date,
            $type,
            $withRaces
        );

        $meetings = new EloquentResourceCollection($collection, $this->meetingResource);

        if ($withRaces) {
            foreach ($meetings as $meeting) {
                $this->loadTotesForMeeting($meeting);
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
            $this->loadTotesForMeeting($model);
        }

        return $model;
    }

    protected function loadTotesForMeeting(MeetingResource $meeting)
    {
        $products = $meeting->getModel()->products;
        dd($products);
        $products = new EloquentResourceCollection($products, 'TopBetta\Resources\ProductResource');

        foreach ($meeting->races as $race) {
            $race->setProducts($products);
        }

        return $meeting;
    }

}