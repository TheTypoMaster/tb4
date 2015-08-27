<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:30 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;
use TopBetta\Resources\PaginatedEloquentResourceCollection;
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

    /**
     * @param $tournamentId
     * @param int $limit
     * @param bool $onlyQualified
     * @return PaginatedEloquentResourceCollection
     */
    public function getTournamentLeaderboard($tournamentId, $limit = 50, $onlyQualified = false)
    {
        $leaderboard = $this->leaderboardRepository->getTournamentLeaderboardPaginated($tournamentId, $limit);

        $leaderboard = new PaginatedEloquentResourceCollection($leaderboard, 'TopBetta\Resources\Tournaments\LeaderboardResource');

        if ($leaderboard->count()) {
            return $this->assignPositions($leaderboard, $this->leaderboardRepository->getLeaderBoardPositionForUser($leaderboard->first()->user_id, $tournamentId));
        }

        return $leaderboard;
    }

    protected function assignPositions($leaderboard, $startPosition = 1)
    {
        $position = $startPosition;
        $lastCurrency = $leaderboard->first()->currency;
        $index = $startPosition;

        foreach ($leaderboard as $record) {
            if( $record->currency != $lastCurrency ) {
                $position = $index;
            }

            if( $record->qualified() ) {
                $record->setPosition($position);
            } else {
                $record->setPosition('-');
            }

            $lastCurrency = $record->currency;

            $index++;
        }

        return $leaderboard;
    }
}