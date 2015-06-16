<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/05/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting;

use Log;
use TopBetta\Repositories\BetResultRepo;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;

class EventBetResultingQueueService {

    /**
     * @var BetResultRepo
     */
    private $betResultRepo;
    /**
     * @var TournamentBetResultService
     */
    private $tournamentBetResultService;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepositoryInterface;

    public function __construct(EventRepositoryInterface $eventRepositoryInterface, BetResultRepo $betResultRepo, TournamentBetResultService $tournamentBetResultService)
    {
        $this->betResultRepo = $betResultRepo;
        $this->tournamentBetResultService = $tournamentBetResultService;
        $this->eventRepositoryInterface = $eventRepositoryInterface;
    }

    public function fire($job, $data)
    {
        if( ! $eventId = array_get($data, 'event_id', null) ) {
            Log::error("No event id specified");
            return false;
        }

        $result = $this->betResultRepo->resultAllBetsForEvent($eventId);

        $tournamentResult = $this->tournamentBetResultService->resultAllBetsForEvent(
            $this->eventRepositoryInterface->find($eventId)
        );

        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("BET RESULTING FAILED " . print_r($data,true));
    }
}