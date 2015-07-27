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
use TopBetta\Services\Racing\RaceResultService;
use TopBetta\Services\Resources\Betting\BetResourceService;

class RaceResourceService {

    /**
     * @var SelectionResourceService
     */
    private $selectionService;
    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var BetResourceService
     */
    private $betResourceService;
    /**
     * @var RaceResultService
     */
    private $resultService;


    public function __construct(EventModelRepositoryInterface $eventRepository, SelectionResourceService $selectionService, BetResourceService $betResourceService, RaceResultService $resultService)
    {
        $this->selectionService = $selectionService;
        $this->eventRepository = $eventRepository;
        $this->betResourceService = $betResourceService;
        $this->resultService = $resultService;
    }

    public function getRaceWithSelections($raceId)
    {
        $race = $this->eventRepository->getEvent($raceId, true);

        $race = new RaceResource($race);

        $race->setRelation('bets', $this->betResourceService->getBetsByEventForAuthUser($raceId));

        $this->resultService->loadResultForRace($race);

        if( ! $race ) {
            throw new ModelNotFoundException;
        }

        return $race;

    }

    public function isOpen($race)
    {
        return $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_SELLING;
    }
}