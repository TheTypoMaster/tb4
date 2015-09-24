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

    public function getFilteredMarketsWithselectionsForEvents($events, $types)
    {
        $markets = $this->marketRepository->getMarketsForEvents($events, $types);

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

    public function getAllMarketsForEventGrouped($event)
    {
        $markets = $this->marketRepository->getMarketsForEvent($event);

        //get the selections
        $selections = $this->selectionResourceService->getSelectionsForEvent($event);

        //create the collection
        $markets = new EloquentResourceCollection($markets, 'TopBetta\Resources\Sports\MarketResource');

        //set the selection relation
        $markets->setRelations('selections', 'market_id', $selections);

        $marketArray = $markets->toArray();

        $groups = array();
        // loop on each market
        foreach($marketArray as $market) {
            // check if group exists
            if(!array_key_exists($market['markettypegroup']['id'], $groups)) $groups[$market['markettypegroup']['id']] = $market['markettypegroup'];
            $index = $market['markettypegroup']['id'];
            $groups[$index]['markets'][] = $market;
        }

        // remove market
        $groupArray = array();
        foreach($groups as &$group){
            $marketArray = array();
            foreach($group['markets'] as &$market) {
                unset($market['markettypegroup']);
            }
            $groupArray[] = $group;
        }
        return $groupArray;
    }

}