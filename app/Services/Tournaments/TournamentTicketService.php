<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/05/2015
 * Time: 1:28 PM
 */

namespace TopBetta\Services\Tournaments;

use Illuminate\Contracts\Validation\UnauthorizedException;
use Log;
use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Services\Exceptions\UnauthorizedAccessException;
use TopBetta\Services\Resources\Tournaments\TicketResourceService;
use TopBetta\Services\Resources\Tournaments\TournamentResourceService;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;

class TournamentTicketService {

    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $tournamentTicketRepository;
    /**
     * @var TournamentTransactionService
     */
    private $transactionService;
    /**
     * @var TicketResourceService
     */
    private $ticketResourceService;

    public function __construct(TournamentTicketRepositoryInterface $tournamentTicketRepository, TournamentTransactionService $transactionService, TicketResourceService $ticketResourceService)
    {
        $this->tournamentTicketRepository = $tournamentTicketRepository;
        $this->transactionService = $transactionService;
        $this->ticketResourceService = $ticketResourceService;
    }

    /**
     * Creates a tournament ticket for the user
     * @param $tournament
     * @param $user
     * @return mixed
     * @throws TournamentEntryException
     */
    public function createTournamentTicketForUser($tournament, $user)
    {
        $ticket =  $this->tournamentTicketRepository->create(array(
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'created_date' => Carbon::now()->toDateTimeString(),
        ));

        if ( ! $ticket ) {
            throw new TournamentEntryException("Error creating ticket");
        }

        return $this->tournamentTicketRepository->find($ticket['id']);
    }

    public function getLimitedTournamentFreeBuyinsForPeriod($user, $period, $startDate = null)
    {
        //get the start and end dates of the period
        if( $startDate ) {
            $start = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->{ 'startOf' . ucfirst($period) }();
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->{ 'endOf' . ucfirst($period) }();
        } else {
            $start = Carbon::now()->{ 'startOf' . ucfirst($period) }();
            $end = Carbon::now()->{ 'endOf' . ucfirst($period) }();
        }


        $tickets = $this->tournamentTicketRepository->getLimitedFreeTicketsForUserBetween($user->id, $start, $end);

        return $tickets;
    }

    /**
     * Gets the total cost of the ticket
     * @param $ticket
     * @return mixed
     */
    public function getTicketValue($ticket)
    {
        return $ticket->tournament->buy_in + $ticket->tournament->entry_fee +
            $ticket->rebuy_count * ( $ticket->tournament->rebuy_buyin + $ticket->tournament->rebuy_entry ) +
            $ticket->topup_count * ( $ticket->tournament->topup_buyin + $ticket->tournament->topup_entry );
    }

    /**
     * Creates a tournament refund transaction
     * @param $ticket
     * @return bool|null
     */
    public function refundTicket($ticket)
    {
        Log::info("TournamentTicketService: Refunding ticket " . $ticket->id);

        //validation
        if( $ticket->refunded_flag ) {
            Log::error("TournamentTicketService: Ticket " . $ticket->id . "already refunded");
            return null;
        }

        if( $ticket->resulted_flag ) {
            Log::error("TournamentTicketService: Ticket " . $ticket->id . " already resulted");
            return null;
        }

        $value = $this->getTicketValue($ticket);

        //create refund transaction
        $transaction = $this->transactionService->createRefundTransaction($ticket->user_id, $value);

        //update tickets with refund info
        $this->tournamentTicketRepository->updateWithId($ticket->id, array(
            "refunded_flag" => true,
            "result_transaction_id" => $transaction['id'],
        ));

        return $transaction;
    }

    public function removeTournamentTicketForUser($tournament, $userId)
    {
        $ticket = $this->tournamentTicketRepository->getTicketByUserAndTournament($userId, $tournament->id);

        //delete all tournament bet for ticket
        foreach($ticket->bets as $bet) {
            $bet->delete();
        }

        $this->refundTicket($ticket);

        return $ticket->delete();
    }

    public function getAndValidateTicketForAuthUser($ticket)
    {
        $ticket = $this->tournamentTicketRepository->find($ticket);

        if( ! \Auth::check() || $ticket->user_id != \Auth::user()->id ) {
            throw new UnauthorizedException("Ticket does not belong to user");
        }

        return $ticket;
    }

    public function availableCurrencyForTicket($ticket)
    {
        if( ! $ticket->tournament->reinvest_winnings_flag ) {
            return $ticket->tournament->start_currency +
                $ticket->rebuy_count * $ticket->tournament->rebuy_currency +
                $ticket->topup_count * $ticket->tournament->topup_currency -
                $ticket->bets->sum(function($v){ return $v->bet_amount; });
        }

        $totalUnresulted = $ticket->bets->whereLoose('resulted_flag', 0)->sum(function($v) { return $v->bet_amount; });

        return $ticket->leaderboard->currency - $totalUnresulted;
    }

    public function getTicketsForUser($user, $type = 'all')
    {
        switch($type)
        {
            case 'all':
                return $this->ticketResourceService->getAllTicketsForUser($user);
            case 'active':
                return $this->ticketResourceService->getActiveTicketsForUser($user);
        }

        throw new \InvalidArgumentException("Type " . $type . " is invalid");
    }

    public function getTicketsForUserByDate($user, $date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);

        return $this->ticketResourceService->getTicketsForUserOnDate($user, $date);
    }

    public function getTicketForUser($ticket, $user)
    {
        $ticket = $this->ticketResourceService->getTicket($ticket);

        if( $ticket->userId != $user->id ) {
            throw new UnauthorizedAccessException;
        }

        return $ticket;
    }

    /**
     * Validates a ticket is able to be created for user in tournament
     * @param $user \TopBetta\Models\UserModel
     * @param $tournament \TopBetta\Models\TournamentModel
     * @throws TournamentEntryException
     */
    public function validateForCreation($user, $tournament)
    {
        if( $tournament->entryClosed() ) {
            throw new TournamentEntryException("Tournament is closed");
        }

        if( $tournament->buy_in > 0 && ! $user->isTopBetta) {
            throw new TournamentEntryException("You have a basic account. Please upgrade it to enter a paid tournament");
        }

        if( $this->tournamentTicketRepository->getTicketByUserAndTournament($user->id, $tournament->id) ) {
            throw new TournamentEntryException("You already have a ticket for this tournament");
        }
    }
}