<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/09/2015
 * Time: 4:48 PM
 */

namespace TopBetta\Repositories\Cache;

use Cache;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Resources\EloquentResourceCollection;

abstract class CachedResourceRepository {

    /**
     * @var \TopBetta\Repositories\BaseEloquentRepository
     */
    protected $repository;

    protected $collectionKeys = array();

    protected $resourceClass;

    protected $cachePrefix;

    protected $relationsToLoad = array();

    protected $storeIndividualResource = true;

    protected $cacheForever = false;

    protected $tags = array();

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->repository, $method), $arguments);
    }

    public function get($key)
    {
        $item = Cache::tags($this->tags)->get($key);

        if ($item) {
            return $this->createResourceFromArray($item);
        }

        return $item;
    }

    /**
     * @param $key
     * @return EloquentResourceCollection
     */
    public function getCollection($key, $resource = null)
    {
        $collection = Cache::tags($this->tags)->get($key);

        if ($collection) {
            return $this->createCollectionFromArray($collection, $resource);
        }

        return new EloquentResourceCollection(new Collection(), $this->resourceClass);
    }

    public function put($key, $model, $time)
    {
        if ($this->cacheForever) {
            Cache::tags($this->tags)->forever($key, $model);
        } else {
            Cache::tags($this->tags)->put($key, $model, $time);
        }

        return $this;
    }

    public function forever($key, $model)
    {
        Cache::tags($this->tags)->forever($key, $model);
    }

    public function putInCollection(&$collection, $key, $model)
    {
        $collection[$key] = $model->toArray();
        return $this;
    }

    public function create($data)
    {
        $model = $this->repository->createAndReturnModel($data);

        return $this->makeCacheResource($model);
    }

    public function update($model, $data)
    {
        $model = $this->repository->update($model, $data);

        return $this->makeCacheResource($model);
    }

    public function updateWithId($id, $data)
    {
        $model = $this->repository->updateWithIdAndReturnModel($id, $data);

        return $this->makeCacheResource($model);
    }

    public function updateOrCreate($data, $criteria)
    {
        $model = $this->repository->updateOrCreateAndReturnModel($data, $criteria);

        return $this->makeCacheResource($model);
    }

    public function makeCacheResource($model)
    {
        $resource = $this->createResource($model);

        if ($this->storeIndividualResource) {
            \Log::debug(get_class($this) . "Putting object in cache KEY " . $this->cachePrefix . $model->id . ' TIME ' . $this->getModelCacheTime($model));
            $this->put($this->cachePrefix . $model->id, $resource->toArray(), $this->getModelCacheTime($model));
        }

        $this->addToCollections($resource);

        return $model;
    }

    public function save($resource)
    {
        if ($this->storeIndividualResource) {
            \Log::debug(get_class($this) . "Putting object in cache KEY " . $this->cachePrefix . $resource->id . ' TIME ' . $this->getModelCacheTime($resource));
            $this->put($this->cachePrefix . $resource->id, $resource->toArray(), $this->getModelCacheTime($resource));
        }

        $this->addToCollections($resource);

        return $this;
    }

    protected function addToCollections($resource)
    {
        foreach ($this->collectionKeys as $collectionKey) {
            $this->addToCollection($resource, $collectionKey, $relations = array());
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
            Cache::tags($this->tags)->forever($key, $collection->toArray());
        } else {
            Cache::tags($this->tags)->put($key, $collection->toArray(), $this->getCollectionCacheTime($collectionKey, $resource));
        }

        return $this;
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        return null;
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        return 1;
    }

    protected function getModelCacheTime($model)
    {
        return 1;
    }

    protected function createResource($model)
    {
        return new $this->resourceClass($model->load($this->relationsToLoad));
    }

    protected function createResourceFromArray($array)
    {
        $class = $this->resourceClass;
        return $class::createResourceFromArray($array);
    }

    protected function createCollectionFromArray($array, $resource = null)
    {
        return EloquentResourceCollection::createFromArray($array, $resource ? : $this->resourceClass)->keyBy('id');
    }


}