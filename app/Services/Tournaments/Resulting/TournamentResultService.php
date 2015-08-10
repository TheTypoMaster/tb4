<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;

class TournamentResultService {

    public function __construct(TournamentLeaderboardRepositoryInterface $leaderboardRepository)
    public function getTournamentResults($tournament)
    {}

    public function getCashTournamentResults($tournamet)
    {}

    public function getJackpotTournamentResults($tournament)
    {}
}