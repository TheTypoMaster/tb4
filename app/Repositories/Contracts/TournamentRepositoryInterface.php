<?php namespace TopBetta\Repositories\Contracts; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 3/04/15
 * File creation time: 22:41
 * Project: tb4
 */
 
interface TournamentRepositoryInterface
{
    public function updateTournamentByEventGroupId($eventGroupId, $closeDate);

    public function search($search);

    public function tournamentOfTheDay($todVenue, $day = null);

    public function findCurrentJackpotTournamentsByType($type, $excludedTournaments = null);

    public function getFinishedUnresultedTournaments();

    public function getUnresultedTournamentsByCompetition($competition);

}