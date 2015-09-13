<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/09/2015
 * Time: 10:56 AM
 */

namespace TopBetta\Repositories\Cache\Sports;


use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;

class SelectionRepository extends CachedResourceRepository {

    protected $tags = array("sports", "selections");

    protected $storeIndividual = false;

    protected $resourceClass = 'TopBetta\Resources\Sports\SelectionResource';

    //protected $relationsToLoad = array('price', 'result');
    /**
     * @var
     */
    private $marketRepository;

    public function __construct(SelectionRepositoryInterface $repository, MarketRepository $marketRepository)
    {
        $this->repository = $repository;
        $this->marketRepository = $marketRepository;
    }

    public function makeCacheResource($model)
    {
//        $resource = $this->createResource($model);
//
//        $this->marketRepository->addSelection($resource);

        return $model;
    }

    public function addSelectionToMarket($model, $market, $eventId, $eventStartDate)
    {
        $resource = $this->createResource($model);

        if ($this->canStoreSelection($resource)) {
            $this->marketRepository->addSelectionToMarket($resource, $market, $eventId, $eventStartDate);
        } else {
            $this->marketRepository->removeSelectionFromMarket($resource, $market, $eventId, $eventStartDate);
        }


        return $model;
    }

    public function canStoreSelection($resource)
    {
        return $resource->selection_status_id == 1 && $resource->getPrice() > 1;
    }
}