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

    const COLLECTION_EVENT_MARKETS = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\MarketResource';

    protected $selectionResourceClass = 'TopBetta\Resources\Sports\SelectionResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("sports", "markets");

    protected $collectionKeys = array(
        self::COLLECTION_EVENT_MARKETS,
    );

    protected $storeIndividualResource = false;

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var MarketTypeRepository
     */
    private $marketTypeRepository;
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(MarketModelRepositoryInterface $repository, SelectionRepositoryInterface $selectionRepository, MarketTypeRepository $marketTypeRepository, EventRepository $eventRepository)
    {
        $this->repository = $repository;
        $this->selectionRepository = $selectionRepository;
        $this->marketTypeRepository = $marketTypeRepository;
        $this->eventRepository = $eventRepository;
    }

    public function getMarketsArrayForEvent($event)
    {
        return Cache::tags($this->tags)->get($this->cachePrefix . 'event_' . $event);
    }

    public function getMarketsForEvent($event)
    {
        $markets = $this->getCollection($this->cachePrefix . 'event_' . $event);

        if (!$markets) {
            return new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        return $markets;
    }

    public function getMarketsForEventAsArray($event)
    {
        return Cache::tags($this->tags)->get($this->cachePrefix . 'event_' . $event);
    }

    public function getFilteredMarketsForEvent($event, $types)
    {
        $markets = $this->getMarketsForEvent($event);

        if (!$markets) {
            return new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }
        $filteredMarkets = $markets->filter(function ($v) use ($types) {return in_array($v->market_type_id, $types);});

        return $filteredMarkets;
    }

    public function storeMarketsForEvent($markets, $event)
    {
        $marketResources = new EloquentResourceCollection($markets, $this->resourceClass);

        $marketResources = $marketResources->filter(function ($v) {
            $v->setRelation('selections', $v->selections->filter(function ($q) {
                return $this->canStoreSelection($q);
            }));

            return $this->canStoreMarket($v) && $v->selections->count();
        });

        if ($marketResources->first()) {
            $this->eventRepository->addVisibleResource($event);

            \Cache::tags($this->tags)->put($this->cachePrefix.'event_'.$event->id, $marketResources->toKeyedArray(), $this->getCollectionCacheTime(self::COLLECTION_EVENT_MARKETS, $marketResources->first()));

            $types = $markets->load('markettype')->pluck('markettype');

            $this->marketTypeRepository->storeMarketTypesForCompetition($types, $event->competition->first());
        }
    }

    public function canStoreSelection($resource)
    {
        return $resource->selection_status_id == 1 && $resource->getPrice() > 1;
    }

    public function makeCacheResource($model)
    {

        if ($this->canStoreMarket($model)) {
            $markets = $this->getMarketsForEventAsArray($model->event_id);

            if ($markets && $market = array_get($markets, $model->id)) {
                $markets[$model->id] = $this->createResource($model)->toArray();
                Cache::tags($this->tags)->put($this->cachePrefix .'event_' . $model->event_id, $markets, $this->getCollectionCacheTime(self::COLLECTION_EVENT_MARKETS, $model));
            }

            $competition = $model->event->competition->first();

            if ($competition) {
                $this->marketTypeRepository->addMarketTypeToCompetition($competition, $model->markettype);
            }
        } else {
            $this->removeMarketIfExists($model);
        }

        return $model;
    }

    public function removeMarketIfExists($marketModel)
    {
        $markets = $this->getMarketsForEventAsArray($marketModel->event_id);

        if (!$markets) {
            return;
        }

        if (!$market = array_get($markets, $marketModel->id)) {
            return;
        }

        unset($markets[$marketModel->id]);

        if(!count($markets)) {
            $this->eventRepository->removeVisibleResource($marketModel->event);
            Cache::forget($this->cachePrefix .'event_' . $marketModel->event_id);
        }

        Cache::tags($this->tags)->put($this->cachePrefix .'event_' . $marketModel->event_id, $markets, $this->getCollectionCacheTime(self::COLLECTION_EVENT_MARKETS, $marketModel));
    }

    public function canStoreMarket($model)
    {
        return ($model->market_status != 'D' && $model->market_status != 'S') && (bool)$model->display_flag;
    }

    public function addSelectionToMarket($selection, $marketModel, $eventId, $eventDate)
    {
        if ($this->canStoreMarket($marketModel)) {

            $markets = $this->getMarketsForEventAsArray($eventId);

            if (!$markets) {
                $markets = array();
            }

            if (!$market = array_get($markets, $selection->market_id)) {
                $markets[$marketModel->id] = $this->createResource($marketModel)->toArray();
                $market = $markets[$marketModel->id];
                $this->makeCacheResource($marketModel);
                $this->eventRepository->addVisibleResource($marketModel->event);
            }


            if (!array_get($market, 'selections')) {
                $market['selections'] = array();
            }

            $market['selections'][$selection->id] = $selection->toArray();
            $markets[$selection->market_id] = $market;

            Cache::tags($this->tags)->put($this->cachePrefix .'event_' . $eventId, $markets, Carbon::createFromFormat('Y-m-d H:i:s', $eventDate)->startOfDay()->addDays(2)->diffInMinutes());

        }
    }

    public function removeSelectionFromMarket($selection, $marketModel, $eventId, $eventDate)
    {
        if ($this->canStoreMarket($marketModel)) {

            $markets = $this->getMarketsForEventAsArray($eventId);

            if (!$markets) {
                return;
            }

            if (!$market = array_get($markets, $selection->market_id)) {
                return;
            }

            unset($market['selections'][$selection->id]);

            if(!count($market['selections'])) {
                unset($markets[$marketModel->id]);
            }

            if(!count($markets)) {
                $markets = null;
                $this->eventRepository->removeVisibleResource($marketModel->event);
            }

            Cache::tags($this->tags)->put($this->cachePrefix .'event_' . $eventId, $markets, Carbon::createFromFormat('Y-m-d H:i:s', $eventDate)->startOfDay()->addDays(2)->diffInMinutes());
        }
    }

    protected function addToCollection($resource, $collectionKey, $resourceClass = null)
    {
        $key = $this->getCollectionCacheKey($collectionKey, $resource);

        if (!$key) {
            return $this;
        }

        if (!$collection = $this->getCollection($key)) {
            $collection = new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        $collection->put($resource->id, $resource);

        \Log::debug(get_class($this) . "Putting object in cache collection KEY " . $key . ' TIME ' . $this->getCollectionCacheTime($collectionKey, $resource));

        if ($this->cacheForever) {
            Cache::tags($this->tags)->forever($key, $collection->toKeyedArray());
        } else {
            Cache::tags($this->tags)->put($key, $collection->toKeyedArray(), $this->getCollectionCacheTime($collectionKey, $resource));
        }

        return $this;
    }

    protected function createResource($model)
    {
        $resource = parent::createResource($model);

        $resource->setRelation('selections', new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\SelectionResource'));

        return $resource;
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_EVENT_MARKETS:
                return $this->cachePrefix . 'event_' . $model->getModel()->event->id;
        }

        throw new \InvalidArgumentException("invalid key" . $keyTemplate);
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_EVENT_MARKETS:
                return $this->getModelCacheTime($model);
        }

        throw new \InvalidArgumentException("invalid key" . $collectionKey);
    }

    protected function getModelCacheTime($model)
    {
        if (!$date=$model->getModel()->event->start_date) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }
}