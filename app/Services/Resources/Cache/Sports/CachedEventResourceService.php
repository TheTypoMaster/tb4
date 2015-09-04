<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 9:41 AM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
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


    public function __construct(EventResourceService $resourceService, EventRepository $eventRepository, MarketRepository $marketRepository)
    {
        $this->resourceService = $resourceService;
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
    }

    public function getEventsForCompetition($competitionId)
    {
        return $this->eventRepository->getEventsForCompetition($competitionId);
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
}