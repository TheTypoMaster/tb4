<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 6:37 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class EventRepository extends CachedSportResourceRepository {

    const CACHE_KEY_PREFIX = 'events_';

    const COLLECTION_COMPETITION_EVENTS = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\EventResource';

    protected $nextToJumpResource = 'TopBetta\Resources\Sports\NextToJumpResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("sports", "events");

    protected $nextToJumpTags = array("events", "n2j");

    protected $parentCollectionKey = self::COLLECTION_COMPETITION_EVENTS;


    /**
     * @var
     */
    private $marketTypeRepository;
    /**
     * @var
     */
    private $marketRepository;

    public function __construct(EventRepositoryInterface $repository,
                                MarketTypeRepositoryInterface $marketTypeRepository,
                                MarketRepositoryInterface $marketRepository,
                                CompetitionRepository $competitionRepository)
    {
        $this->repository = $repository;
        $this->marketTypeRepository = $marketTypeRepository;
        $this->marketRepository = $marketRepository;
    }

    public function getEventsForCompetition($id)
    {
        return $this->getCollection($this->cachePrefix . 'competition_' . $id);
    }

    public function getEventsArrayForCompetition($id)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . 'competition_' . $id);
    }

    public function getEventArray($id)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . $id);
    }

    public function getEvent($id)
    {
        return $this->get($this->cachePrefix . $id);
    }

    public function addTeamsToModel($model, $teams)
    {
        $changes = $this->repository->addTeamsToModel($model, $teams);

        if (count($changes)) {
            $this->makeCacheResource($model->load('teams'));
        }

        return $model;
    }

    public function nextToJump()
    {
        return Cache::tags($this->nextToJumpTags)->get($this->cachePrefix . 'n2j');
    }

    public function updateNextToJump($limit = 10)
    {
        $events = $this->repository->getNextToJumpSports($limit);

        Cache::tags($this->nextToJumpTags)->forever($this->cachePrefix . 'n2j', new EloquentResourceCollection($events, $this->nextToJumpResource));
    }

    public function addModelToCompetition($model, $competition)
    {
        if (!$model->competition->first()) {
            $this->repository->addModelToCompetition($model, $competition);
        }

        $this->addToCollection($this->createResource($model), self::COLLECTION_COMPETITION_EVENTS);

        return $model;
    }


    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_COMPETITION_EVENTS:
                if (!$model->getModel()->competition->first()) {
                    return null;
                }
                return $this->cachePrefix .'competition_'.$model->getModel()->competition->first()->id;
        }

        throw new \InvalidArgumentException("invalid key" . $keyTemplate);
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_COMPETITION_EVENTS:
                return $this->getModelCacheTime($model);
        }

        throw new \InvalidArgumentException("invalid key" . $collectionKey);
    }

    protected function getModelCacheTime($model)
    {
        if (!$date=$model->start_date) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }

    protected function setParentRepository()
    {
        $this->parentRepository = \App::make('TopBetta\Repositories\Cache\Sports\CompetitionRepository');
    }

    protected function getParentResource($model)
    {
        return $model->competition->first();
    }

    protected function getParentResourceCollection($id)
    {
        return $this->getEventsForCompetition($id);
    }

}