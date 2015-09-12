<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:30 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;
use TopBetta\Resources\Tournaments\TournamentLeaderboard;

class LeaderboardResourceService {

    /**
     * @var TournamentLeaderboardRepositoryInterface
     */
    private $leaderboardRepository;

    public function __construct(TournamentLeaderboardRepositoryInterface $leaderboardRepository)
    {
        $this->leaderboardRepository = $leaderboardRepository;
    }

    public function getTournamentLeaderboard($tournamentId, $limit = 50, $onlyQualified = false)
    {
        $leaderboard = $this->leaderboardRepository->getTournamentLeaderboardCollection($tournamentId, $limit, true);

        if (! $onlyQualified && $leaderboard->count() < $limit) {
            $leaderboard = $leaderboard->merge(
                $this->leaderboardRepository->getTournamentLeaderboardCollection($tournamentId, $limit - $leaderboard->count(), false)
            );
        }

        return new TournamentLeaderboard($leaderboard, 'TopBetta\Resources\Tournaments\LeaderboardResource');
    }
}