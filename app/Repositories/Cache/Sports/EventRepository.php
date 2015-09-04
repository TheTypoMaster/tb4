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

class EventRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'events_';

    protected $resourceClass = 'TopBetta\Resources\Sports\EventResource';

    protected $nextToJumpResource = 'TopBetta\Resources\Sports\NextToJumpResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $competitionEventsTag = array("competition", "events");

    protected $nextToJumpTags = array("events", "n2j");

    /**
     * @var
     */
    private $marketTypeRepository;
    /**
     * @var
     */
    private $marketRepository;

    public function __construct(EventRepositoryInterface $repository, MarketTypeRepositoryInterface $marketTypeRepository, MarketRepositoryInterface $marketRepository)
    {
        $this->repository = $repository;
        $this->marketTypeRepository = $marketTypeRepository;
        $this->marketRepository = $marketRepository;
    }

    public function getEventsForCompetition($id)
    {
        return Cache::tags($this->competitionEventsTag)->get($this->cachePrefix . 'competition_' . $id);
    }

    public function nextToJump()
    {
        return Cache::tags($this->nextToJumpTags)->get($this->cachePrefix . 'n2j');
    }

    public function updateEvents()
    {
        $events = $this->repository->getVisibleEvents();

        Cache::tags($this->competitionEventsTag)->flush();
        $competitionCollections = array();
        foreach ($events as $event) {
            if (!$competitionEvents = array_get($competitionCollections, $event->getModel()->competition->first()->id)) {
                $competitionEvents = new EloquentResourceCollection(new Collection(), $this->resourceClass);
            }

            $this->putInCollection($competitionEvents, $event->id, $this->createResource($event));
            $competitionCollections[$event->getModel()->competition->first()->id] = $competitionEvents;
        }

        foreach ($competitionCollections as $key => $collection) {
            Cache::tags($this->competitionEventsTag)->forever($this->cachePrefix . 'competition_' . $key, $collection);
        }
    }

    public function updateNextToJump()
    {
        $events = $this->repository->getNextToJumpSports();

        Cache::tags($this->nextToJumpTags)->forever($this->cachePrefix . 'n2j', new EloquentResourceCollection($events, $this->nextToJumpResource));
    }


    protected function getModelCacheTime($model)
    {
        if (!$date=$model->start_date) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }
}