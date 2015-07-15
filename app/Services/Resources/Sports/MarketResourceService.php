<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 2:11 PM
 */

namespace TopBetta\Services\Resources\Sports;

use TopBetta\Repositories\Contracts\MarketModelRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class MarketResourceService {

    /**
     * @var MarketModelRepositoryInterface
     */
    private $marketRepository;

    public function __construct(MarketModelRepositoryInterface $marketRepository)
    {
        $this->marketRepository = $marketRepository;
    }

    public function getMarketsForCompetition($competition, $types = null)
    {
        $markets = $this->marketRepository->getMarketsForCompetition($competition, $types);

        return new EloquentResourceCollection($markets, 'TopBetta\Resources\Sports\MarketResource');
    }
}