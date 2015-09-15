<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 9:41 AM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Cache\CachedResourceService;
use TopBetta\Services\Resources\Sports\EventResourceService;

class CachedEventResourceService extends CachedResourceService {

    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var MarketRepository
     */
    private $marketRepository;
    /**
     * @var CachedMarketResourceService
     */
    private $marketResourceService;


    public function __construct(EventResourceService $resourceService, EventRepository $eventRepository, MarketRepository $marketRepository, CachedMarketResourceService $marketResourceService)
    {
        $this->resourceService = $resourceService;
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
        $this->marketResourceService = $marketResourceService;
    }

    public function nextToJump()
    {
        $nextToJump = $this->eventRepository->nextToJump();

        if (!$nextToJump) {
            return $this->resourceService->nextToJump();
        }

        return $nextToJump;
    }

    public function getEventsForCompetition($competitionId)
    {
        $events = $this->eventRepository->getEventsForCompetition($competitionId);

        if (!$events) {
            return new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\EventResource');
        }

        return $this->filterEvents($events);
    }

    public function getEventsArrayForCompetition($competitionId)
    {
        $events = $this->eventRepository->getEventsArrayForCompetition($competitionId);

        if (!$events) {
            return array();
        }

        return $this->filterEventsArray($events);
    }

    public function getEventsForCompetitionWithFilteredMarkets($competition, $types)
    {
        $events = $this->getEventsForCompetition($competition);

        if (!$events) {
            return $this->resourceService->getEventsForCompetitionWithFilteredMarkets($competition, $types);
        }

        foreach ($events as $event) {
             $event->setRelation('markets', $this->marketRepository->getFilteredMarketsForEvent($event->id, $types));
        }

        return $events;
    }

    public function filterEvents($events)
    {
        return $events->filter(function ($v) {
            $markets = $this->marketRepository->getMarketsArrayForEvent($v->id);

            return (bool) (count($markets) && $v->display_flag);
        });
    }

    public function filterEventsArray($events)
    {
        return array_filter($events, function ($v) {
            $markets = $this->marketRepository->getMarketsArrayForEvent($v['id']);

            return (bool) (count($markets) && $v['display_flag']);
        });
    }
}