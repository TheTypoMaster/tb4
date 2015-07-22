<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 2:09 PM
 */
namespace TopBetta\Repositories\Contracts;

use TopBetta\Repositories\Carbon;

interface TournamentTicketRepositoryInterface
{
    /**
     * @param $tournamentId
     * @return mixed
     */
    public function getTicketsInTournament($tournamentId);

    public function getWithUserAndTournament($ticketId);

    public function getTicketByUserAndTournament($userId, $tournamentId);

    public function getRecentAndActiveTicketsForUserWithTournament($user);

    public function nextToJumpTicketsForUser($user, $limit = 10);

    public function getActiveTicketsForUser($user);

    public function getTicketsForUserOnDate($user, \Carbon\Carbon $date);

    public function getAllForUserPaginated($user);
}