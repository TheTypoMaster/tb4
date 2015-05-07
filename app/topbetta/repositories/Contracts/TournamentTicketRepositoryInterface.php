<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 2:09 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentTicketRepositoryInterface
{
    /**
     * @param $tournamentId
     * @return mixed
     */
    public function getTicketsInTournament($tournamentId);

    public function getWithUserAndTournament($ticketId);

    public function getTicketByUserAndTournament($userId, $tournamentId);
}