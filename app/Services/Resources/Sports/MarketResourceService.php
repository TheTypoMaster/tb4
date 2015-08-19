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
use TopBetta\Services\Betting\SelectionService;

class MarketResourceService {

    /**
     * @var MarketModelRepositoryInterface
     */
    private $marketRepository;
    /**
     * @var SelectionResourceService
     */
    private $selectionResourceService;

    public function __construct(MarketModelRepositoryInterface $marketRepository, SelectionResourceService $selectionResourceService)
    {
        $this->marketRepository = $marketRepository;
        $this->selectionResourceService = $selectionResourceService;
    }

    public function getMarketsForCompetition($competition, $types = null)
    {
        $markets = $this->marketRepository->getMarketsForCompetition($competition, $types);

        $selections = $this->selectionResourceService->getSelectionsForMarkets($markets->lists('id')->all());

        $markets = new EloquentResourceCollection($markets, 'TopBetta\Resources\Sports\MarketResource');

        $markets->setRelations('selections', 'market_id', $selections);

        return $markets;
    }

    public function getAllMarketsForEvent($event)
    {
        $markets = $this->marketRepository->getMarketsForEvent($event);

        //get the selections
        $selections = $this->selectionResourceService->getSelectionsForEvent($event);

        //create the collection
        $markets = new EloquentResourceCollection($markets, 'TopBetta\Resources\Sports\MarketResource');

        //set the selection relation
        $markets->setRelations('selections', 'market_id', $selections);

        return $markets;
    }
}