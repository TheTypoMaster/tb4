<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 12:32 PM
 */

namespace TopBetta\Repositories\Cache;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Models\SelectionModel;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\PriceResource;

class RacingSelectionRepository extends CachedResourceRepository
{
    protected static $modelClass = 'TopBetta\Models\SelectionModel';

    const COLLECTION_RACES_SELECTIONS = 0;

    protected $cachePrefix = 'selections_';

    protected $resourceClass = 'TopBetta\Resources\SelectionResource';

    protected $storeIndividualResource = false;

    protected $tags = array("racing", "selections");

    protected $collectionKeys = array(
        self::COLLECTION_RACES_SELECTIONS,
    );

    public function __construct(SelectionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function update($model, $data)
    {
        if ($model->isDirty()) {
            $model = parent::update($model, $data);
        }

        return $model;
    }

    public function getSelectionByExternalId($selectionId)
    {
        $data = \Cache::tags($this->tags)->get($this->cachePrefix . $selectionId);

        if (!$data) {
            return $this->repository->getSelectionByExternalId($selectionId);
        }

        $model = new SelectionModel($data);
        $model->syncOriginal();
        $model->exists = true;

        return $model;
    }

    public function makeCacheResource($model)
    {
        $model = parent::makeCacheResource($model);

        \Cache::tags($this->tags)->put($this->cachePrefix . $model->external_selection_id, $model->toArray(), Carbon::now()->addDays(1)->diffInMinutes());

        return $model;
    }

    public function getSelectionsForRace($raceId)
    {
        return $this->getCollection($this->cachePrefix . '_race_' . $raceId);
    }

    public function getSelectionsArrayForRace($raceId)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . '_race_' . $raceId);
    }

    public function updatePricesForSelectionInRace($selectionId, $race, $price)
    {
        if ($selections = $this->getSelectionsArrayForRace($race['id'])) {
            if ($selection = array_get($selections, $selectionId)) {

                if (!array_get($selection, 'prices')) {
                    $selection['prices'] = array();
                }

                $priceKey = null;
                foreach ($selection['prices'] as $key=>$selectionPrice) {
                    if ($selectionPrice['id'] == $price->id) {
                        $priceKey = $key;
                        break;
                    }
                }

                if ($priceKey) {
                    $selection['prices'][$priceKey] = $this->createPriceResource($price)->toArray();
                } else {
                    $selection['prices'][] = $this->createPriceResource($price)->toArray();
                }

                $selections[$selection['id']] = $selection;
                \Cache::tags($this->tags)->put($this->cachePrefix . '_race_' . $race['id'], $selections, $this->getRaceCollectionTime($race));
            }
        }
    }

    protected function addToCollection($resource, $collectionKey, $resourceClass = null)
    {
        $key = $this->getCollectionCacheKey($collectionKey, $resource);

        if (!$key) {
            return $this;
        }

        if (!$collection = $this->getCollection($key, $resourceClass)) {
            $collection = new EloquentResourceCollection(new Collection(), $resourceClass ? : $this->resourceClass);
        }

        $collection->put($resource->id, $resource);

        \Log::debug(get_class($this) . "Putting object in cache collection KEY " . $key . ' TIME ' . $this->getCollectionCacheTime($collectionKey, $resource));

        if ($this->cacheForever) {
            \Cache::tags($this->tags)->forever($key, $collection->toKeyedArray());
        } else {
            \Cache::tags($this->tags)->put($key, $collection->toKeyedArray(), $this->getCollectionCacheTime($collectionKey, $resource));
        }

        return $this;
    }

    protected function getRaceCollectionTime($race)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $race['start_date'])->startOfDay()->addDays(2)->diffInMinutes();
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_RACES_SELECTIONS:
                return $this->cachePrefix . '_race_' . $model->getModel()->market->event_id;
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_RACES_SELECTIONS:
                return Carbon::createFromFormat('Y-m-d H:i:s', $model->market->event->start_date)->startOfDay()->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function createPriceResource($price)
    {
        return new PriceResource($price);
    }
}