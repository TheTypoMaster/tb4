<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:16 AM
 */

namespace TopBetta\Services\Resources;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Resources\RaceResource;

class RaceResourceService {

    /**
     * @var SelectionResourceService
     */
    private $selectionService;
    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;


    public function __construct(EventModelRepositoryInterface $eventRepository, SelectionResourceService $selectionService)
    {
        $this->selectionService = $selectionService;
        $this->eventRepository = $eventRepository;
    }

    public function getRaceWithSelections($raceId)
    {
        $race = $this->eventRepository->getEvent($raceId, true);

        if( ! $race ) {
            throw new ModelNotFoundException;
        }

        return new RaceResource($race);
    }

    public function isOpen($race)
    {
        return $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_SELLING;
    }
}