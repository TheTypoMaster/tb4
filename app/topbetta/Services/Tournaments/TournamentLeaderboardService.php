<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 4:09 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\DbTournamentLeaderboardRepository;

class TournamentLeaderboardService {

    /**
     * @var DbTournamentLeaderboardRepository
     */
    private $leaderboardRepository;

    public function __construct(DbTournamentLeaderboardRepository $leaderboardRepository)
    {
        $this->leaderboardRepository = $leaderboardRepository;
    }

    public function increaseCurrency($leaderboardId, $amount)
    {
        $leaderboard = $this->leaderboardRepository->find($leaderboardId);

        return $this->leaderboardRepository->updateWithId($leaderboardId, array("currency" => $leaderboard->currency + $amount));
    }

    public function decreaseCurrency($leaderboardId, $amount)
    {
        return $this->increaseCurrency($leaderboardId, -$amount);
    }

}