<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 11:54 AM
 */

namespace TopBetta\Services\Resources\Sports;

use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class EventResourceService {

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var MarketResourceService
     */
    private $marketResourceService;

    public function __construct(EventRepositoryInterface $eventRepository, MarketResourceService $marketResourceService)
    {
        $this->eventRepository = $eventRepository;
        $this->marketResourceService = $marketResourceService;
    }

    public function nextToJump()
    {
        $nextToJump = $this->eventRepository->getNextToJumpSports();

        return new EloquentResourceCollection($nextToJump, 'TopBetta\Resources\Sports\NextToJumpResource');
    }

    public function getEventsForCompetition($competitionId)
    {
        $events = $this->eventRepository->getEventsForCompetition($competitionId);

        return new EloquentResourceCollection($events, 'TopBetta\Resources\Sports\EventResource');
    }

    public function getEventsForCompetitionWithFilteredMarkets($competitionId, $types)
    {
        $events = $this->eventRepository->getEventsForCompetition($competitionId);

        $markets = $this->marketResourceService->getFilteredMarketsWithSelectionsForEvents($events->lists('id')->all(), $types);

        $events = new EloquentResourceCollection($events, 'TopBetta\Resources\Sports\EventResource');

        $events->setRelations('markets', 'event_id', $markets);

        return $events;
    }
}