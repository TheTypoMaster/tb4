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

class CompetitionRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'competitions_';

    protected $resourceClass = 'TopBetta\Resources\Sports\CompetitionResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $baseCompetitionCollectionTag = array('baseCompetitions', 'competitions');

    public function __construct(CompetitionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getCompetitionsForBaseCompetition($id)
    {
        return Cache::tags($this->baseCompetitionCollectionTag)->get($this->cachePrefix . 'base_competition_' . $id);
    }

    public function updateVisibleCompetitionsForBaseCompetition()
    {
        $competitions = $this->repository->getVisibleCompetitions();

        $collections = array();

        foreach ($competitions as $competition) {
            if (!$collection = array_get($collections, $competition->base_competition_id)) {
                $collection = new EloquentResourceCollection(new Collection(), $this->resourceClass);
            }

            $collection->push($this->createResource($competition));
            $collections[$competition->base_competition_id] = $collection;
        }

        //flush old competitions
        Cache::tags($this->baseCompetitionCollectionTag)->flush();

        foreach ($collections as $key => $collection) {
            Cache::tags($this->baseCompetitionCollectionTag)->forever($this->cachePrefix . 'base_competition_' . $key, $collection);
        }
    }

    protected function getModelCacheTime($model)
    {
        if (!$date=$model->start_date) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }
}