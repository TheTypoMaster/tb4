<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 11:07 AM
 */

namespace TopBetta\Services\Resources\Cache;


use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Services\Resources\RaceResourceService;

class CachedRaceResourceService extends CachedResourceService {

    /**
     * @var RaceRepository
     */
    private $raceRepository;

    public function __construct(RaceResourceService $resourceService, RaceRepository $raceRepository)
    {
        $this->resourceService = $resourceService;
        $this->raceRepository = $raceRepository;
    }

    public function getRacesForMeeting($meetingId)
    {
        $races = $this->raceRepository->getRacesForMeeting($meetingId);

        if (!$races) {
            return $this->resourceService->getRacesForMeeting($meetingId);
        }

        return $races;
    }


}