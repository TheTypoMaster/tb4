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
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;
use TopBetta\Services\Tournaments\Resulting\TournamentResulter;

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
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;
    /**
     * @var TournamentResulter
     */
    private $tournamentResulter;

    public function __construct(EventRepositoryInterface $eventRepositoryInterface,
                                BetResultRepo $betResultRepo,
                                TournamentBetResultService $tournamentBetResultService,
                                TournamentRepositoryInterface $tournamentRepository,
                                TournamentResulter $tournamentResulter)
    {
        $this->betResultRepo = $betResultRepo;
        $this->tournamentBetResultService = $tournamentBetResultService;
        $this->eventRepositoryInterface = $eventRepositoryInterface;
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentResulter = $tournamentResulter;
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

        $tournaments = $this->tournamentRepository->getFinishedUnresultedTournaments();

        foreach ($tournaments as $tournament) {
            $this->tournamentResulter->resultTournament($tournament);
        }

        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("BET RESULTING FAILED " . print_r($data,true));
    }
}