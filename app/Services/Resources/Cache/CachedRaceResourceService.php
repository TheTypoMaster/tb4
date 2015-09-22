<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 11:07 AM
 */

namespace TopBetta\Services\Resources\Cache;


use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Betting\BetResourceService;
use TopBetta\Services\Resources\RaceResourceService;

class CachedRaceResourceService extends CachedResourceService {

    /**
     * @var RaceRepository
     */
    private $raceRepository;
    /**
     * @var CachedSelectionResourceService
     */
    private $selectionResourceService;
    /**
     * @var BetResourceService
     */
    private $betResourceService;

    public function __construct(RaceResourceService $resourceService, RaceRepository $raceRepository, CachedSelectionResourceService $selectionResourceService, BetResourceService $betResourceService)
    {
        $this->resourceService = $resourceService;
        $this->raceRepository = $raceRepository;
        $this->selectionResourceService = $selectionResourceService;
        $this->betResourceService = $betResourceService;
    }

    public function getRace($id)
    {
        $race = $this->raceRepository->getRace($id);

        if (!$race) {
            return $this->resourceService->getRace($id);
        }

        $this->loadTotesForRace($race);

        return $race;
    }

    public function getRacesForMeeting($meetingId)
    {
        $races = $this->raceRepository->getRacesForMeeting($meetingId);

        if (!$races->count()) {
            return $this->resourceService->getRacesForMeeting($meetingId);
        }

        return $races;
    }

    public function getRaceWithSelections($raceId)
    {
        $race = $this->raceRepository->getRace($raceId);

        if (!$race) {
            return $this->resourceService->getRaceWithSelections($raceId);
        }

        $race->setSelections($this->selectionResourceService->getSelectionsForRace($race->id));

        $race->setRelation('bets', $this->betResourceService->getBetsByEventForAuthUser($raceId));

        $this->loadTotesForRace($race);

        return $race;
    }


}