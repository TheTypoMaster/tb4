<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/08/2015
 * Time: 1:52 PM
 */

namespace TopBetta\Services\Tournaments\Queue;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Services\Tournaments\TournamentBetService;
use TopBetta\Services\Tournaments\TournamentService;

class TournamentBetRefundQueueService {


    /**
     * @var TournamentBetService
     */
    private $tournamentBetService;
    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(TournamentBetService $tournamentBetService, TournamentService $tournamentService, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->tournamentBetService = $tournamentBetService;
        $this->tournamentService = $tournamentService;
        $this->competitionRepository = $competitionRepository;
    }


    public function fire($job, $data)
    {
        if (! $eventId = array_get($data, 'event_id')) {
            \Log::error("TournamentBetRefundQueueService: no event id specified");
        }

        $this->tournamentBetService->refundBetsForEvent($eventId);

        $this->tournamentService->refundAbandonedTournamentsForEvent($eventId);

        $job->delete();
    }

    public function failed($data)
    {
        \Log::error("TournamentBetRefundQueueService: REFUNDING FAILED " . print_r($data,true));
    }
}