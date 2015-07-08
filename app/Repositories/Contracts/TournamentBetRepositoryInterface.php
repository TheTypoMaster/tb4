<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/05/2015
 * Time: 2:36 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentBetRepositoryInterface
{
    /**
     * @param $ticketId
     * @return mixed
     */
    public function getResultedUserBetsInTournament($ticketId);

    public function getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $eventStatuses);

    public function getBetsForEventByStatusIn($eventId, $status, $betType = null);

    public function getBetsForSelection($selectionId);

    public function getBetsForMarket($marketId);
}