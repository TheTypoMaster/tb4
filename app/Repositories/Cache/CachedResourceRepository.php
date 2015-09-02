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

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->repository, $method), $arguments);
    }

    public function find($id)
    {
        if ($resource = Cache::get($this->cachePrefix . $id)) {
            return $resource;
        }

        return $this->createResource($this->repository->find($id));
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function put($key, $model, $time)
    {
        Cache::put($key, $model, $time);
        return $this;
    }

    public function putInCollection($collection, $key, $model)
    {
        $collection->put($key, $model);
        return $this;
    }

    public function create($data, $relations = array())
    {
        $model = $this->repository->createAndReturnModel($data);

        return $this->makeCacheResource($model, $relations);
    }

    public function updateWithId($id, $data, $relations = array())
    {
        $model = $this->repository->updateWithIdAndReturnModel($id, $data);

        return $this->makeCacheResource($model, $relations);
    }

    public function updateOrCreate($data, $criteria, $relations = array())
    {
        $model = $this->repository->updateOrCreateAndReturnModel($data, $criteria);

        return $this->makeCacheResource($model, $relations);
    }

    public function makeCacheResource($model, $relations = array())
    {
        $resource = $this->createResource($model);

        if ($this->storeIndividualResource) {
            \Log::debug(get_class($this) . "Putting object in cache KEY " . $this->cachePrefix . $model->id . ' TIME ' . $this->getModelCacheTime($model));
            $this->put($this->cachePrefix . $model->id, $resource, $this->getModelCacheTime($model));
        }

        $this->addToCollections($resource, $relations);

        return $model;
    }

    public function save($resource)
    {
        if ($this->storeIndividualResource) {
            \Log::debug(get_class($this) . "Putting object in cache KEY " . $this->cachePrefix . $resource->id . ' TIME ' . $this->getModelCacheTime($resource));
            $this->put($this->cachePrefix . $resource->id, $resource, $this->getModelCacheTime($resource));
        }

        $this->addToCollections($resource);

        return $this;
    }

    protected function addToCollections($resource, $relations = array())
    {
        foreach ($this->collectionKeys as $collectionKey) {
            $this->addToCollection($resource, $collectionKey, $relations = array());
        }
    }

    protected function addToCollection($resource, $collectionKey)
    {
        $key = $this->getCollectionCacheKey($collectionKey, $resource);

        if (!$key) {
            return $this;
        }

        if (!$collection = Cache::get($key)) {
            $collection = new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        $this->putInCollection($collection, $resource->id, $resource);

        \Log::debug(get_class($this) . "Putting object in cache collection KEY " . $key . ' TIME ' . $this->getCollectionCacheTime($collectionKey, $resource));
        Cache::put($key, $collection, $this->getCollectionCacheTime($collectionKey, $resource));

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


}