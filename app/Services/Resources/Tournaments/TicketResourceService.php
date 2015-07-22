<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/07/2015
 * Time: 3:11 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
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

        return $this->createTicketCollection($tickets);
    }

    public function getActiveTicketsForUser($user)
    {
        $tickets = $this->ticketRepository->getActiveTicketsForUser($user);

        return $this->createTicketCollection($tickets);
    }

    public function getTicketsForUserOnDate($user, Carbon $date)
    {
        $tickets = $this->ticketRepository->getTicketsForUserOnDate($user, $date);

        return $this->createTicketCollection($tickets);
    }

    protected function createTicketCollection($tickets)
    {
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
}