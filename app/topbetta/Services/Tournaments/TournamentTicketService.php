<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/05/2015
 * Time: 1:28 PM
 */

namespace TopBetta\Services\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;

class TournamentTicketService {

    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $tournamentTicketRepository;

    public function __construct(TournamentTicketRepositoryInterface $tournamentTicketRepository)
    {
        $this->tournamentTicketRepository = $tournamentTicketRepository;
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
            throw new TournamentEntryException("Error createing ticket");
        }

        return $ticket;
    }

    public function getFreeBuyinsForPeriod($user, $period, $startDate = null)
    {
        //get the start and end dates of the period
        if( $startDate ) {
            $start = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->{ 'startOf' . ucfirst($period) }();
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->{ 'endOf' . ucfirst($period) }();
        } else {
            $start = Carbon::now()->{ 'startOf' . ucfirst($period) }();
            $end = Carbon::now()->{ 'endOf' . ucfirst($period) }();
        }


        $tickets = $this->tournamentTicketRepository->getTicketsForUserByBuyinBetween($user->id, 0, $start, $end);

        return $tickets;
    }
}