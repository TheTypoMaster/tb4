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

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function nextToJump()
    {
        $nextToJump = $this->eventRepository->getNextToJumpSports();

        return new EloquentResourceCollection($nextToJump, 'TopBetta\Resources\Sports\NextToJumpResource');
    }
}