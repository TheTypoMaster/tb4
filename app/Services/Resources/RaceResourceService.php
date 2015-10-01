<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:16 AM
 */

namespace TopBetta\Services\Resources;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\ProductProviderMatchRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\RaceResource;
use TopBetta\Services\Products\ProductService;
use TopBetta\Services\Racing\RaceResultService;
use TopBetta\Services\Resources\Betting\BetResourceService;

class RaceResourceService {

    /**
     * @var SelectionResourceService
     */
    private $selectionService;
    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var ProductProviderMatchRepositoryInterface
     */
    private $productProviderMatchRepositoryInterface;
	/**
     * @var BetResourceService
     */
    private $betResourceService;
    /**
     * @var RaceResultService
     */
    private $resultService;
    /**
     * @var ProductService
     */
    private $productService;


    public function __construct(EventModelRepositoryInterface $eventRepository, SelectionResourceService $selectionService, ProductProviderMatchRepositoryInterface $productProviderMatchRepositoryInterface, BetResourceService $betResourceService, RaceResultService $resultService, ProductService $productService)
    {
        $this->selectionService = $selectionService;
        $this->eventRepository = $eventRepository;
        $this->productProviderMatchRepositoryInterface = $productProviderMatchRepositoryInterface;
		$this->betResourceService = $betResourceService;
        $this->resultService = $resultService;
        $this->productService = $productService;
    }

    public function getRace($id)
    {
        $race = $this->eventRepository->find($id)->load('eventstatus');

        $race = new RaceResource($race);

        $this->loadTotesForRace($race);

        return $race;
    }

    public function getRaceWithSelections($raceId)
    {
        $race = $this->eventRepository->getEvent($raceId);

        $race = new RaceResource($race);

        $race->setSelections($this->selectionService->getSelectionsForRace($race->id));

        $race->setRelation('bets', $this->betResourceService->getBetsByEventForAuthUser($raceId));

        $this->resultService->loadResultForRace($race);

        if( ! $race ) {
            throw new ModelNotFoundException;
        }

        $this->loadTotesForRace($race);

        return $race;
    }

    public function getRacesForMeeting($meetingId)
    {
        $races = $this->eventRepository->getEventsForCompetition($meetingId);

        return new EloquentResourceCollection($races, 'TopBetta\Resources\RaceResource');
    }

    public function loadTotesForRace(RaceResource $race)
    {
        $products = $this->productService->getAuthUserProductsForCompetition($race->getModel()->competition->first());

        $products = new EloquentResourceCollection($products, 'TopBetta\Resources\ProductResource');

        if(!$race->model->fixed_odds_enabled){
            $products = $products->filter(function ($v) {

                return !$v->is_fixed_odds == '1';
            });
        }

        $race->setProducts($products);
    }

    public function isOpen($race)
    {
        return $race->status == EventStatusRepositoryInterface::STATUS_SELLING;
    }
}