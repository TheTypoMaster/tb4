<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/09/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Repositories\Cache\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Resources\EloquentResourceCollection;

abstract class CachedSportResourceRepository extends CachedResourceRepository {

    protected $parentRepository = null;

    protected $parentCollectionKey;

    abstract protected function setParentRepository();

    abstract protected function getParentResource($model);

    abstract protected function getParentResourceCollection($id);

    protected function getParentRepository()
    {
        if (!$this->parentRepository) {
            $this->setParentRepository();
        }

        return $this->parentRepository;
    }

    protected function canStore($model)
    {
        return (bool)$model->display_flag;
    }

    public function makeCacheResource($model)
    {
        $model = parent::makeCacheResource($model);

        $this->updateVisibleResource($model);

        return $model;
    }

    public function addVisibleResource($model)
    {
        if ($this->canStore($model) && $parent = $this->getParentResource($model)) {
            $resources = $this->getParentResourceCollection($parent->id);

            if(!$resources) {
                $resources = new EloquentResourceCollection(new Collection(), $this->resourceClass);

                if ($this->getParentRepository()) {
                    $this->getParentRepository()->addVisibleResource($parent);
                }
            }

            $resource = $this->createResource($model);
            $resources->put($model->id, $resource);

            if (!$this->cacheForever) {
                \Cache::tags($this->tags)->put(
                    $this->getCollectionCacheKey($this->parentCollectionKey, $resource),
                    $resources->toArray(),
                    $this->getCollectionCacheTime($this->parentCollectionKey, $resource)
                );
            } else {
                \Cache::tags($this->tags)->forever(
                    $this->getCollectionCacheKey($this->parentCollectionKey, $resource),
                    $resources->toArray()
                );
            }

        }
    }

    public function removeVisibleResource($model)
    {
        if ($parent = $this->getParentResource($model)) {
            $resources = $this->getParentResourceCollection($parent->id);

            if($resources && $resource = $resources->get($model->id)) {
                $resources->forget($model->id);

                if (!$resources->count()) {
                    \Cache::tags($this->tags)->forget($this->getCollectionCacheKey($this->parentCollectionKey, $resource));

                    if ($this->getParentRepository()) {
                        $this->getParentRepository()->removeVisibleResource($parent);
                    }
                } else {
                    if (!$this->cacheForever) {
                        \Cache::tags($this->tags)->put(
                            $this->getCollectionCacheKey($this->parentCollectionKey, $resource),
                            $resources->toArray(),
                            $this->getCollectionCacheTime($this->parentCollectionKey, $resource)
                        );
                    } else {
                        \Cache::tags($this->tags)->forever(
                            $this->getCollectionCacheKey($this->parentCollectionKey, $resource),
                            $resources->toArray()
                        );
                    }
                }
            }
        }
    }

    public function updateVisibleResource($model)
    {
        if ($parent = $this->getParentResource($model)) {
            $resources = $this->getParentResourceCollection($parent->id);

            if ($resources && $resource = $resources->get($model->id)) {
                if ($this->canStore($model)) {
                    $resource = $this->createResource($model);
                    $resources->put($model->id, $resource);
                } else {
                    $resources->forget($model->id);
                }

                if (!$resources->count()) {
                    \Cache::tags($this->tags)->forget($this->getCollectionCacheKey($this->parentCollectionKey, $resource));
                    if ($this->getParentRepository()) {
                        $this->getParentRepository()->removeVisibleResource($parent);
                    }
                } else {
                    if (!$this->cacheForever) {
                        \Cache::tags($this->tags)->put(
                            $this->getCollectionCacheKey($this->parentCollectionKey, $resource),
                            $resources->toArray(),
                            $this->getCollectionCacheTime($this->parentCollectionKey, $resource)
                        );
                    } else {
                        \Cache::tags($this->tags)->forever(
                            $this->getCollectionCacheKey($this->parentCollectionKey, $resource),
                            $resources->toArray()
                        );
                    }
                }
            }
        }
    }
}