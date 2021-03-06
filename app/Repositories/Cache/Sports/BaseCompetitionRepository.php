<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 5:08 PM
 */

namespace TopBetta\Repositories\Cache\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class BaseCompetitionRepository extends CachedSportResourceRepository {

    const CACHE_KEY_PREFIX = 'base_competitions_';

    const COLLECTION_BASE_COMPETITIONS_SPORT = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\BaseCompetitionResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $cacheForever = true;

    protected $tags = array("sports", "baseCompetitions");

    protected $parentCollectionKey = self::COLLECTION_BASE_COMPETITIONS_SPORT;

    /**
     * @var SportRepository
     */
    private $sportRepository;

    public function __construct(BaseCompetitionRepositoryInterface $repository, SportRepository $sportRepository)
    {
        $this->repository = $repository;
        $this->sportRepository = $sportRepository;
    }

    public function getBaseCompetition($id)
    {
        return $this->get($this->cachePrefix . $id);
    }

    public function getBaseCompetitionsBySport($sport)
    {
        return $this->getCollection($this->cachePrefix . 'sport_' . $sport);
    }

    public function getBaseCompetitionsArrayBySport($sport)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . 'sport_' . $sport);
    }

    public function getCollectionCacheKey($key, $model)
    {
        switch ($key) {
            case self::COLLECTION_BASE_COMPETITIONS_SPORT:
                return $this->cachePrefix . 'sport_' . $model->sport_id;
        }

        throw new \InvalidArgumentException("Invalid key " . $key);
    }

    protected function setParentRepository()
    {
        $this->parentRepository = \App::make('TopBetta\Repositories\Cache\Sports\SportRepository');
    }

    protected function getParentResource($model)
    {
        return $model->sport;
    }

    protected function getParentResourceCollection($id)
    {
        return $this->getBaseCompetitionsBySport($id);
    }


}