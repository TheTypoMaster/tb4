<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/07/2015
 * Time: 2:21 PM
 */

namespace TopBetta\Services\Resources\Sports;


use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class MarketTypeResourceService {

    /**
     * @var MarketTypeRepositoryInterface
     */
    private $marketTypeRepository;

    public function __construct(MarketTypeRepositoryInterface $marketTypeRepository)
    {
        $this->marketTypeRepository = $marketTypeRepository;
    }

    public function getMarketTypesForCompetition($competition)
    {
        $marketTypes = $this->marketTypeRepository->getAvailableMarketTypesForCompetition($competition);

        return new EloquentResourceCollection($marketTypes, 'TopBetta\Resources\Sports\MarketTypeResource');
    }
}