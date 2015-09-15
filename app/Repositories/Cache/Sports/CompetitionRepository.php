<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 5:10 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class CompetitionRepository extends CachedSportResourceRepository {

    const CACHE_KEY_PREFIX = 'competitions_';

    const COLLECTION_COMPETITION_BASE_COMPETITION = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\CompetitionResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;


    protected $tags = array("sports", "competitions");
    /**
     * @var BaseCompetitionRepository
     */
    private $baseCompetitionRepository;

    public function __construct(CompetitionRepositoryInterface $repository, BaseCompetitionRepository $baseCompetitionRepository)
    {
        $this->repository = $repository;
        $this->baseCompetitionRepository = $baseCompetitionRepository;
    }

    public function getCompetitionsForBaseCompetition($id)
    {
        return $this->getCollection($this->cachePrefix . 'base_competition_' . $id);
    }

    public function getCompetition($id)
    {
        return $this->get($this->cachePrefix . $id);
    }

    public function getVisibleCompetitionByBaseCompetition($baseCompetition)
    {
        $competitions = $this->getCollection($this->cachePrefix . 'base_competition_' . $baseCompetition);

        if (!$competitions) {
            return new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        return $competitions;
    }

    public function getVisibleCompetitionsArrayByBaseCompetition($baseCompetition)
    {
        $competitions = \Cache::tags($this->tags)->get($this->cachePrefix . 'base_competition_' . $baseCompetition);

        if (!$competitions) {
            return array();
        }

        return $competitions;
    }

    public function findOrGetFromDb($id)
    {
        $competition = $this->getCompetition($id);

        if (!$competition) {
            return $this->repository->find($id);
        }

        return $competition;
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_COMPETITION_BASE_COMPETITION:
                return $this->cachePrefix . 'base_competition_' . $model->base_competition_id;
        }

        throw new \InvalidArgumentException("invalid key " . $keyTemplate);
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_COMPETITION_BASE_COMPETITION:
                return $this->getModelCacheTime($model);
        }

        throw new \InvalidArgumentException("invalid key " . $collectionKey);
    }

    protected function getModelCacheTime($model)
    {
        if (!$date=$model->close_time) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }

    protected function setParentRepository()
    {
        $this->parentRepository = \App::make('TopBetta\Repositories\Cache\Sports\BaseCompetitionRepository');
    }

    protected function getParentResource($model)
    {
        return $model->baseCompetition;
    }

    protected function getParentResourceCollection($id)
    {
        return $this->getCompetitionsForBaseCompetition($id);
    }
}