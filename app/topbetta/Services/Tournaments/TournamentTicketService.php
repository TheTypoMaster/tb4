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

    public function createTournamentTicketForUser($tournament, $user)
    {
        $ticket =  $this->tournamentTicketRepository->create(array(
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'created_date' => Carbon::now()->toDateTimeString(),
            'updated_date' => Carbon::now()->toDateTimeString(),
        ));

        if ( ! $ticket ) {
            throw new TournamentEntryException("Error createing ticket");
        }

        return $ticket;
    }
}