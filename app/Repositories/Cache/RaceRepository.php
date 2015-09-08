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
use TopBetta\Resources\SmallRaceResource;

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

    protected $tags = array("racing", "races");
    /**
     * @var MeetingRepository
     */
    private $meetingRepository;

    public function __construct(EventRepositoryInterface $repository, MeetingRepository $meetingRepository)
    {
        $this->repository = $repository;
        $this->meetingRepository = $meetingRepository;
    }

    public function getRacesForMeeting($meetingId)
    {
        return $this->getCollection($this->cachePrefix . '_meeting_' . $meetingId);
    }

    public function getRace($raceId)
    {
        return $this->get($this->cachePrefix . $raceId);
    }

    public function addModelToCompetition($model, $competition)
    {
        if (!$model->competition->first()) {
            $this->repository->addModelToCompetition($model, $competition);

            $resource = $this->createSmallRaceResource($model);
            $this->meetingRepository->addSmallRace($resource, $model->competition->first());
        }

        $this->addToCollection($this->createResource($model), self::COLLECTION_MEETING_RACES);

        return $model;
    }

    public function makeCacheResource($model)
    {
        $model = parent::makeCacheResource($model);

        if ($model->competition->first()) {
            $resource = $this->createSmallRaceResource($model);

            $this->meetingRepository->addSmallRace($resource, $model->competition->first());
        }

        return $model;
    }

    public function save($resource)
    {
        parent::save($resource);

        $model = $resource->getModel();

        if ($model->competition->first()) {
            $resource = $this->createSmallRaceResource($model);

            $this->meetingRepository->addSmallRace($resource, $model->competition->first());
        }

        return $this;
    }

    public function put($key, $model, $time)
    {
        $oldRace = $this->get($key);

        //make sure we dont remove race results on update
        if ($oldRace && $oldRace->getResults() && ! array_get($model, 'results')) {
            $model['result_string'] = $oldRace->getResultString();
            $model['results'] = $oldRace->getResults();
            $model['exotic_results'] = $oldRace->getExoticResults();
        }

        return parent::put($key, $model, $time);
    }

    public function addToCollection($resource, $collectionKey)
    {
        $collection = $this->getCollection($this->getCollectionCacheKey($collectionKey, $resource));

        if ($collection) {
            $oldRace = $collection->get($resource->id);

            //make sure we dont remove race results on update
            if ($oldRace && $oldRace->getResults() && ! $resource->getResults()) {
                $resource->setResultString($oldRace->getResultString());
                $resource->setResults($oldRace->getResults());
                $resource->setExoticResults($oldRace->getExoticResults());
            }
        }

        return parent::addToCollection($resource, $collectionKey);
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

    protected function createSmallRaceResource($model)
    {
        $resource = new SmallRaceResource($model);

        $oldRace = $this->get($this->cachePrefix . $model->id);

        //make sure we dont remove race results on update
        if ($oldRace && $oldRace->getResults() && ! $resource->getResults()) {
            $resource->setResultString($oldRace->getResultString());
            $resource->setResults($oldRace->getResults());
            $resource->setExoticResults($oldRace->getExoticResults());
        }

        return $resource;
    }
}