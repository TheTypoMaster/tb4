<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 9:36 AM
 */
namespace TopBetta\Repositories\Contracts;

use Carbon\Carbon;

interface TournamentGroupRepositoryInterface
{
    public function getVisibleSportTournamentGroupsWithTournaments(Carbon $date = null);

    public function getVisibleRacingTournamentGroupsWithTournaments(Carbon $date = null);

    public function getByName($name);

    public function search($term);
}