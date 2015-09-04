<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 10:13 AM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Services\Resources\Cache\CachedResourceService;
use TopBetta\Services\Resources\Sports\MarketResourceService;


class CachedMarketResourceService extends CachedResourceService  {

    /**
     * @var MarketRepository
     */
    private $marketRepository;

    public function __construct(MarketResourceService $resourceService, MarketRepository $marketRepository)
    {
        $this->resourceService = $resourceService;
        $this->marketRepository = $marketRepository;
    }

    public function getAllMarketsForEvent($event)
    {
        return $this->marketRepository->getMarketsForEvent($event);
    }


}