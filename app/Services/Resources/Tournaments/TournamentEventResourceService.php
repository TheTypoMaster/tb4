<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/09/2015
 * Time: 11:39 AM
 */

namespace TopBetta\Services\Resources\Tournaments;


use Illuminate\Support\Collection;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\RaceResource;
use TopBetta\Resources\Sports\EventResource;

class TournamentEventResourceService {

    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;

    public function __construct(EventModelRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }


    public function getEventResourceForTournament($tournament)
    {
        $events = $this->eventRepository->getByTournamentEventGroup($tournament->event_group_id);

        if ($events instanceof EloquentResourceCollection) {
            return $events;
        }

        return new EloquentResourceCollection($events, 'TopBetta\Resources\Tournaments\TournamentEventResource');
    }
}