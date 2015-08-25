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
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Betting\Factories\BetPlacementFactory;
use TopBetta\Services\Tournaments\Betting\Factories\TournamentBetPlacementFactory;
use TopBetta\Services\Validation\TournamentBetValidator;

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
    /**
     * @var TournamentTicketService
     */
    private $ticketService;
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;

    public function __construct(TournamentBetRepositoryInterface $betRepository, EventService $eventService, BetResultStatusRepositoryInterface $betResultStatusRepository, TournamentBetResultService $resultService, TournamentTicketService $ticketService, BetTypeRepositoryInterface $betTypeRepository)
    {
        $this->betRepository = $betRepository;
        $this->eventService = $eventService;
        $this->betResultStatusRepository = $betResultStatusRepository;
        $this->resultService = $resultService;
        $this->ticketService = $ticketService;
        $this->betTypeRepository = $betTypeRepository;
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

    public function placeBet($bet)
    {
        //validate bet
        $validator = new TournamentBetValidator();
        $validator->validateForCreation($bet);

        $ticket = $this->ticketService->getAndValidateTicketForAuthUser($bet['ticket_id']);

        $betType = $this->betTypeRepository->getBetTypeByName($bet['bet_type']);

        $placementService = TournamentBetPlacementFactory::make($betType->name);
        return $placementService->placeBet($ticket, $bet['selections'], $bet['amount'], $betType);
    }
}