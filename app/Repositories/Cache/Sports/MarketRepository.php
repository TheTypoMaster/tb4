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

    protected $storeIndividual = false;

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var MarketTypeRepository
     */
    private $marketTypeRepository;

    public function __construct(MarketModelRepositoryInterface $repository, SelectionRepositoryInterface $selectionRepository, MarketTypeRepository $marketTypeRepository)
    {
        $this->repository = $repository;
        $this->selectionRepository = $selectionRepository;
        $this->marketTypeRepository = $marketTypeRepository;
    }

    public function getMarketsForEvent($event)
    {
        return $this->get($this->cachePrefix . 'event_' . $event);
    }

    public function getFilteredMarketsForEvent($event, $types)
    {
        $markets = $this->getMarketsForEvent($event);

        $filteredMarkets = $markets->filter(function ($v) use ($types) {return in_array($v->market_type_id, $types);});

        return $filteredMarkets;
    }

    public function makeCacheResource($model)
    {
        $model = parent::makeCacheResource($model);

        $competition = $model->event->competition->first();

        if ($competition) {
            $this->marketTypeRepository->addMarketTypeToCompetition($competition, $model->markettype);
        }

        return $model;
    }

    public function addSelection($selection)
    {
        $tempSelection = clone $selection;

        $markets = $this->getMarketsForEvent($tempSelection->getModel()->market->event->id);

        $market = $markets->get($selection->market_id);

        if ($market) {
            $market->addSelection($selection);
            $this->save($market);
        }
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
                return $this->cachePrefix . 'event_' . $model->event->id;
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