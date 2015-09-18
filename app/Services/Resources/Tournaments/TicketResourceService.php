<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/07/2015
 * Time: 3:11 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\PaginatedEloquentResourceCollection;
use TopBetta\Resources\Tournaments\TicketResource;
use TopBetta\Services\Tournaments\TournamentLeaderboardService;

class TicketResourceService {

    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;
    /**
     * @var TournamentLeaderboardService
     */
    private $leaderboardService;

    public function __construct(TournamentTicketRepositoryInterface $ticketRepository, TournamentLeaderboardService $leaderboardService)
    {
        $this->ticketRepository = $ticketRepository;
        $this->leaderboardService = $leaderboardService;
    }

    public function getRecentAndActiveTicketsForUser($user)
    {
        $tickets = $this->ticketRepository->getRecentAndActiveTicketsForUserWithTournament($user);

        return $this->createTicketCollection($tickets);
    }

    public function nextToJumpTicketsForUser($user)
    {
        $tickets = $this->ticketRepository->nextToJumpTicketsForUser($user);

        return $this->createNextToJumpCollection($tickets);
    }

    public function getActiveTicketsForUser($user, $with = null)
    {
        $tickets = $this->ticketRepository->getActiveTicketsForUser($user);

        if ($with) {
            $tickets->getCollection()->load($with);
        }

        return $this->createTicketCollection($tickets);
    }

    public function getTicketsForUserOnDate($user, Carbon $date, $with = null)
    {
        $tickets = $this->ticketRepository->getTicketsForUserOnDate($user, $date);

        if ($with) {
            $tickets->getCollection()->load($with);
        }

        return $this->createTicketCollection($tickets);
    }

    public function getAllTicketsForUser($user, $with = null)
    {
        $tickets = $this->ticketRepository->getAllForUserPaginated($user);

        $tickets->getCollection()->load('leaderboard');

        if ($with) {
            $tickets->getCollection()->load($with);
        }

        $tickets = new PaginatedEloquentResourceCollection($tickets, 'TopBetta\Resources\Tournaments\TicketResource');

        foreach ($tickets as $ticket) {
            if( $ticket->getQualified() ) {
                $ticket->setPosition(
                    $this->leaderboardService->getLeaderboardPositionForTicket($ticket)
                );
            }
        }

        return $tickets;
    }

    public function getTicket($ticket)
    {
        $ticket = $this->ticketRepository->find($ticket);

        if( ! $ticket ) {
            throw new ModelNotFoundException;
        }

        $ticket->load(array('bets', 'leaderboard'));

        $ticket = new TicketResource($ticket);

        if( $ticket->getQualified() ) {
            $ticket->setPosition($this->leaderboardService->getLeaderboardPositionForTicket($ticket));
        }

        return $ticket;
    }

    protected function createTicketCollection($tickets)
    {
        if ($tickets instanceof EloquentResourceCollection) {
            return $tickets;
        }

        $tickets = new EloquentResourceCollection($tickets, 'TopBetta\Resources\Tournaments\TicketResource');

        foreach($tickets as $ticket) {
            if( $ticket->getQualified() ) {
                $ticket->setPosition(
                    $this->leaderboardService->getLeaderboardPositionForTicket($ticket)
                );
            }
        }

        return $tickets;
    }

    protected function createNextToJumpCollection($tickets)
    {
        $tickets = new EloquentResourceCollection($tickets, 'TopBetta\Resources\Tournaments\NextToJumpTicketResource');

        foreach($tickets as $ticket) {
            if( $ticket->getQualified() ) {
                $ticket->setPosition(
                    $this->leaderboardService->getLeaderboardPositionForTicket($ticket)
                );
            }
        }

        return $tickets;
    }

}