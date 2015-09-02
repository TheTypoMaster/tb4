<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 11:01 AM
 */

namespace TopBetta\Repositories\Cache;

use Carbon\Carbon;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;

class RaceRepository extends CachedResourceRepository {

    const COLLECTION_MEETING_RACES = 0;

    protected $cachePrefix = 'races_';

    protected $resourceClass = 'TopBetta\Resources\RaceResource';

    protected $relationsToLoad = array(
        'eventstatus'
    );

    protected $collectionKeys = array(
        self::COLLECTION_MEETING_RACES
    );

    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getRacesForMeeting($meetingId)
    {
        return $this->get($this->cachePrefix . '_meeting_' . $meetingId);
    }

    public function getRace($raceId)
    {
        return $this->get($this->cachePrefix . $raceId);
    }

    public function addModelToCompetition($model, $competition)
    {
        if (!$model->competition->first()) {
            $this->repository->addModelToCompetition($model, $competition);
        }

        $this->addToCollection($this->createResource($model), self::COLLECTION_MEETING_RACES);

        return $model;
    }

    public function put($key, $model, $time)
    {
        $oldRace = $this->get($key);

        //make sure we dont remove race results on update
        if ($oldRace && $oldRace->getResults() && ! $model->getResults()) {
            $model->setResultString($oldRace->getResultString());
            $model->setResults($oldRace->getResults());
            $model->setExoticResults($oldRace->getExoticResults());
        }

        return parent::put($key, $model, $time);
    }

    public function putInCollection($collection, $key, $model)
    {
        $oldRace = $collection->get($key);

        //make sure we dont remove race results on update
        if ($oldRace && $oldRace->getResults() && ! $model->getResults()) {
            $model->setResultString($oldRace->getResultString());
            $model->setResults($oldRace->getResults());
            $model->setExoticResults($oldRace->getExoticResults());
        }

        return parent::putInCollection($collection, $key, $model);
    }


    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch($keyTemplate) {
            case self::COLLECTION_MEETING_RACES:
                if (!$model->getModel()->competition->first()) {
                    return null;
                }
                return $this->cachePrefix . '_meeting_' . $model->getModel()->competition->first()->id;
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_MEETING_RACES:
                return Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date)->startOfDay()->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getModelCacheTime($model)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date)->startOfDay()->addDays(2)->diffInMinutes();
    }

    protected function getModelKey($model)
    {
        return $this->cachePrefix . '_' . $model->id;
    }
}