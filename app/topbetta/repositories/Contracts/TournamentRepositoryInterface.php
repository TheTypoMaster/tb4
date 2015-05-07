<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 2:06 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentRepositoryInterface
{
    public function updateTournamentByEventGroupId($eventGroupId, $closeDate);

    public function search($search);

    public function tournamentOfTheDay($todVenue, $day = null);

    public function findCurrentTournamentsByType($type, $excludedTournaments = null);
}