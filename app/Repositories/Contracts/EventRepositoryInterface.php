<?php namespace TopBetta\Repositories\Contracts;
use Carbon\Carbon;

/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 17:33
 * Project: tb4
 */

interface EventRepositoryInterface {

    public function getNextToJumpSports($number = 10);

    public function getEventsForCompetition($competitionId);

    public function addTeamPlayers($event, $team, $players);


}