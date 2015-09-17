<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 1:18 PM
 */

namespace TopBetta\Services\Resources\Cache\Sports;



use TopBetta\Repositories\Cache\Sports\MarketTypeRepository;
use TopBetta\Services\Resources\Cache\CachedResourceService;
use TopBetta\Services\Resources\Sports\MarketTypeResourceService;

class CachedMarketTypeResourceService extends CachedResourceService {

    /**
     * @var MarketTypeRepository
     */
    private $marketTypeRepository;

    public function __construct(MarketTypeResourceService $resourceService, MarketTypeRepository $marketTypeRepository)
    {
        $this->marketTypeRepository = $marketTypeRepository;
        $this->resourceService = $resourceService;
    }

    public function getMarketTypesForCompetition($competition)
    {
        return $this->marketTypeRepository->getMarketTypesForCompetition($competition);
    }
}