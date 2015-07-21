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
    public function getVisibleTournamentGroupsWithTournaments(Carbon $date = null);
}