<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 12:50 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Services\Betting\Exceptions\BetPlacementException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Betting\Factories\BetSelectionFactory;
use TopBetta\Services\Tournaments\Betting\TournamentBetLimitService;
use TopBetta\Services\Tournaments\TournamentBetSelectionService;
use TopBetta\Services\Tournaments\TournamentLeaderboardService;
use TopBetta\Services\Tournaments\TournamentTicketService;

abstract class AbstractTournamentBetPlacementService {

    /**
     * @var String
     */
    protected $selectionServiceClass;

    /**
     * @var TournamentBetRepositoryInterface
     */
    protected $betRepository;

    /**
     * @var TournamentBetSelectionService
     */
    protected $selectionService;
    /**
     * @var TournamentBetLimitService
     */
    protected $betLimitService;
    /**
     * @var TournamentTicketService
     */
    private $ticketService;

    protected $product;

    protected $betType;
    /**
     * @var BetResultStatusRepositoryInterface
     */
    private $betResultStatusRepository;
    /**
     * @var TournamentLeaderboardService
     */
    private $leaderboardService;

    public function __construct(TournamentBetRepositoryInterface $betRepository, TournamentBetLimitService $betLimitService, TournamentTicketService $ticketService, BetResultStatusRepositoryInterface $betResultStatusRepository, TournamentLeaderboardService $leaderboardService)
    {
        $this->betRepository = $betRepository;
        $this->selectionService = new TournamentBetSelectionService(\App::make($this->selectionServiceClass));
        $this->betLimitService = $betLimitService;
        $this->ticketService = $ticketService;
        $this->betResultStatusRepository = $betResultStatusRepository;
        $this->leaderboardService = $leaderboardService;
    }

    public function placeBet($ticket, $selections, $amount, $betType)
    {
        //check sufficient funds
        if( $this->ticketService->availableCurrencyForTicket($ticket) < $this->getTotalAmountForBet($selections, $amount) ) {
            throw new BetPlacementException("Insufficient funds");
        }

        //get and validate selections
        $selections = $this->selectionService->getAndValidateSelections($selections, $ticket->tournament);

        //validate tournament
        $this->validateTournamentBet($ticket, $amount, $betType, $selections);

        //check bet limit
        $this->checkBetLimit($ticket, array_unique(array_pluck($selections, 'selection')), $amount, $betType);

        //create bet
        return $this->createBet($ticket, $selections, $amount, $betType);
    }

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $data = array(
            "tournament_ticket_id" => $ticket->id,
            "bet_type_id" => $betType->id,
            "bet_amount" => $amount,
            "bet_product_id" => $this->product ? $this->product->id : 0,
            "bet_result_status_id" => $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->id,
        );

        //create bet
        try {
            $bet = $this->betRepository->create(array_merge($data, $extraData));
        } catch (\Exception $e) {
            \Log::error("ERROR CREATING TOURNAMENT BET " . $e->getMessage());
            throw new BetPlacementException("Error creating bet");
        }

        //create selections
        try {
            $this->selectionService->createBetSelections($bet['id'], $selections);
        } catch( \Exception $e ) {
            \Log::error("ERROR CREATING BET SELECTIONS FOR BET " . $bet['id'] . " MESSAGE " . $e->getMessage() .PHP_EOL . $e->getTraceAsString());
            $this->betRepository->deleteById($bet['id']);
            throw new BetSelectionException(null, "Error creating bet selections");
        }

        $this->leaderboardService->increaseTurnedOver($ticket->tournament_id, $ticket->user_id, $amount);

        return $bet;
    }

    public function validateTournamentBet($ticket, $amount, $betType, $selections)
    {
        //check betting closed
        if ($ticket->tournament->bettingClosed()) {
            throw new BetPlacementException("Betting closed for tournament " . $ticket->tournament->name);
        }
    }

    /**
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function setBetType($betType)
    {
        $this->betType = $betType;
        return $this;
    }

    abstract function getTotalAmountForBet($selections, $amount);

    abstract function checkBetLimit($ticket, $selections, $amount, $betType);
}