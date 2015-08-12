<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/05/2015
 * Time: 1:59 PM
 */

namespace TopBetta\Services\Tournaments;

use Carbon\Carbon;
use Log;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;
use TopBetta\Services\Betting\EventService;

class TournamentBetService {

    /**
     * @var TournamentBetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var BetResultStatusRepositoryInterface
     */
    private $betResultStatusRepository;
    /**
     * @var TournamentBetResultService
     */
    private $resultService;

    public function __construct(TournamentBetRepositoryInterface $betRepository, EventService $eventService, BetResultStatusRepositoryInterface $betResultStatusRepository, TournamentBetResultService $resultService)
    {
        $this->betRepository = $betRepository;
        $this->eventService = $eventService;
        $this->betResultStatusRepository = $betResultStatusRepository;
        $this->resultService = $resultService;
    }

    public function getBetsForUserInTournamentWhereEventClosed($user, $tournament)
    {
        $statuses = $this->eventService->getClosedEventStatusIds();

        return $this->betRepository->getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $statuses);
    }

    public function refundBetsForSelection($selection)
    {
        $bets = $this->betRepository->getBetsForSelection($selection);

        foreach($bets as $bet) {
            Log::info("REFUNDING TOURNAMENT BET " . $bet->id . " FOR SELECTION " . $selection);

            $this->betRepository->updateWithId($bet->id, array(
                "resulted_flag" => true,
                "bet_result_status_id" => $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_FULLY_REFUNDED)->id,
                "win_amount" => $bet->bet_amount,
                "updated_date" => Carbon::now()
            ));
        }

        return $bets;
    }

    public function refundBetsForEvent($eventId)
    {
        $bets = $this->betRepository->getBetsForEventByStatusIn($eventId, array($this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->id));

        foreach ($bets as $bet) {
            $this->resultService->refundBet($bet);
        }
    }
}