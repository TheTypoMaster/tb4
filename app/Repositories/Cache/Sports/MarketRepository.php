<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 7:39 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\MarketModelRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class MarketRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'markets_';

    protected $resourceClass = 'TopBetta\Resources\Sports\MarketResource';

    protected $selectionResourceClass = 'TopBetta\Resources\Sports\SelectionResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $marketCollectionTags = array('event', 'markets');

    protected $storeIndividual = false;

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;

    public function __construct(MarketModelRepositoryInterface $repository, SelectionRepositoryInterface $selectionRepository)
    {
        $this->repository = $repository;
        $this->selectionRepository = $selectionRepository;
    }

    public function getMarketsForEvent($event)
    {
        return Cache::tags($this->marketCollectionTags)->get($this->cachePrefix . 'event_' . $event);
    }

    public function getFilteredMarketsForEvent($event, $types)
    {
        $markets = $this->getMarketsForEvent($event);

        $filteredMarkets = $markets->filter(function ($v) use ($types) {return in_array($v->market_type_id, $types);});

        return $filteredMarkets;
    }


    public function updateMarketsAndSelections()
    {
        $markets = $this->repository->getVisibleMarketsWithSelections();

        $selections = $this->selectionRepository->findIn($markets->lists('selection_id')->all())->load(array('team', 'player', 'price', 'result'));

        $markets = $this->createCollectionAndAttachSelections($markets->unique('id'), $selections);

        $collections = $this->createEventCollections($markets);

        $this->storeEventCollections($collections);
    }

    protected function createCollectionAndAttachSelections($markets, $selections)
    {
        $markets = new EloquentResourceCollection($markets, $this->resourceClass);

        $selections = new EloquentResourceCollection($selections, $this->selectionResourceClass);

        $markets->setRelations('selections', 'market_id', $selections);

        return $markets;
    }

    protected function createEventCollections($markets)
    {
        $eventCollections = array();

        foreach ($markets as $market) {
            if (!$eventCollection = array_get($eventCollections, $market->event_id)) {
                $eventCollection = new EloquentResourceCollection(new Collection(), $this->resourceClass);
            }

            $eventCollection->push($market);
            $eventCollections[$market->event_id] = $eventCollection;
        }

        return $eventCollections;
    }

    protected function storeEventCollections($collections)
    {
        Cache::tags($this->marketCollectionTags)->flush();

        foreach ($collections as $key => $collection) {
            Cache::tags($this->marketCollectionTags)->forever($this->cachePrefix . 'event_' . $key, $collection);
        }
    }

    protected function getModelCacheTime($model)
    {
        if (!$date=$model->getModel()->event->start_date) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }
}