<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 10:13 AM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Cache\CachedResourceService;

use TopBetta\Services\Resources\Sports\MarketResourceService;


class CachedMarketResourceService extends CachedResourceService  {

    /**
     * @var MarketRepository
     */
    private $marketRepository;
    /**
     * @var CachedSelectionResourceService
     */
    private $selectionResourceService;

    public function __construct(MarketResourceService $resourceService, MarketRepository $marketRepository, CachedSelectionResourceService $selectionResourceService)
    {
        $this->resourceService = $resourceService;
        $this->marketRepository = $marketRepository;
        $this->selectionResourceService = $selectionResourceService;
    }

    public function getAllMarketsForEvent($event)
    {
        $markets = $this->marketRepository->getMarketsForEvent($event);

        if (!$markets) {
            return new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\MarketResource');
        }

        return $markets;
    }

    public function getFilteredMarketsForEvent($event, $types)
    {

    }

    protected function filterMarkets($markets)
    {
        return $markets->filter(function ($v) {
            if ($v->market_status == 'D' || $v->market_status == 'S') { return false; }

            $selections = $v->selections;

            if (!$selections->count()) {return false;}

            return (bool) $v->display_flag;
        });
    }
}