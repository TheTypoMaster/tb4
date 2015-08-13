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


    public function __construct(EventModelRepositoryInterface $eventRepository, SelectionResourceService $selectionService, ProductProviderMatchRepositoryInterface $productProviderMatchRepositoryInterface)
    {
        $this->selectionService = $selectionService;
        $this->eventRepository = $eventRepository;
        $this->productProviderMatchRepositoryInterface = $productProviderMatchRepositoryInterface;
    }

    public function getRaceWithSelections($raceId)
    {
        $race = $this->eventRepository->getEvent($raceId, true);

        if( ! $race ) {
            throw new ModelNotFoundException;
        }

        $race = new RaceResource($race);

        $this->loadTotesForRace($race);

        return $race;
    }

    public function loadTotesForRace(RaceResource $race)
    {
        $products = $this->productProviderMatchRepositoryInterface->getProductAndBetTypeByCompetition($race->getModel()->competition->first());

        $products = new EloquentResourceCollection($products, 'TopBetta\Resources\ProductResource');

        $race->setProducts($products);
    }

    public function isOpen($race)
    {
        return $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_SELLING;
    }
}