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

        return $events;
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

    public function getEventWithFilteredMarkets($eventId, $types)
    {
        $event = $this->eventRepository->getEvent($eventId);

        if (!$event) {
            $event = $this->resourceService->getEvent($eventId);
        }

        $event->setRelation('markets', $this->marketRepository->getFilteredMarketsForEvent($event->id, $types));

        return $event;
    }

}