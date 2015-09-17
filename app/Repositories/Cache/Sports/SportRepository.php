<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class SportRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'sports_';

    const COLLECTION_ALL_SPORTS = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\SportResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("sports", "sport");

    protected $allSportsCollectionKey;


    protected $cacheForever = true;
    /**
     * @var BaseCompetitionRepositoryInterface
     */
    private $baseCompetitionRepository;

    public function __construct(SportRepositoryInterface $repository, BaseCompetitionRepositoryInterface $baseCompetitionRepository)
    {
        $this->repository = $repository;
        $this->baseCompetitionRepository = $baseCompetitionRepository;
        $this->allSportsCollectionKey = self::CACHE_KEY_PREFIX . 'all';
    }

    public function getVisibleSports()
    {
        return $this->getCollection($this->allSportsCollectionKey);
    }

    public function makeCacheResource($model)
    {
        $model = parent::makeCacheResource($model);

        $this->updateVisibleResource($model);

        return $model;
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_ALL_SPORTS:
                return $this->cachePrefix . 'all';
        }

        throw new \InvalidArgumentException("Invalid key " . $keyTemplate);
    }

    public function addVisibleResource($model)
    {
        if ($this->canStore($model)) {

            $sports = $this->getVisibleSports();

            if (!$sports) {
                $sports = new EloquentResourceCollection(new Collection(), $this->resourceClass);
            }

            $resource = $this->createResource($model);
            $sports->put($model->id, $resource);

            \Cache::tags($this->tags)->forever(
                $this->getCollectionCacheKey(self::COLLECTION_ALL_SPORTS, $resource),
                $sports->toArray()
            );
        }
    }

    public function removeVisibleResource($model)
    {
        $sports = $this->getVisibleSports();

        if ($sports && $resource = $sports->get($model->id)) {
            $sports->forget($model->id);

            if (!$sports->count()) {
                \Cache::tags($this->tags)->forget($this->getCollectionCacheKey(self::COLLECTION_ALL_SPORTS, $resource));
            } else {
                \Cache::tags($this->tags)->forever(
                    $this->getCollectionCacheKey(self::COLLECTION_ALL_SPORTS, $resource),
                    $sports->toArray()
                );
            }
        }
    }

    public function updateVisibleResource($model)
    {
        $sports = $this->getVisibleSports();

        if ($sports && $resource = $sports->get($model->id)) {

            if (!$this->canStore($model)) {
                $sports->forget($model->id);
            } else {
                $resource = $this->createResource($model);
                $sports->put($model->id, $resource);
            }

            if (!$sports->count()) {
                \Cache::tags($this->tags)->forget($this->getCollectionCacheKey(self::COLLECTION_ALL_SPORTS, $resource));
            } else {
                \Cache::tags($this->tags)->forever(
                    $this->getCollectionCacheKey(self::COLLECTION_ALL_SPORTS, $resource),
                    $sports->toArray()
                );
            }
        }
    }

    public function canStore($model)
    {
        return (bool)$model->display_flag;
    }

}