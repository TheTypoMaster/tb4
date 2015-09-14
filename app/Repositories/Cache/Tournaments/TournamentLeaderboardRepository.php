<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/09/2015
 * Time: 10:22 AM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\PaginatedEloquentResourceCollection;

class TournamentLeaderboardRepository extends CachedResourceRepository implements TournamentLeaderboardRepositoryInterface
{

    const CACHE_KEY_PREFIX = 'tournament_leaderboard_';

    const COLLECTION_TOURNAMENT_LEADERBOARD = 0;

    protected $resourceClass = 'TopBetta\Resources\Tournaments\LeaderboardResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament_leaderboard");

    protected $storeIndividualResource = false;

    protected $collectionKeys = array(
        self::COLLECTION_TOURNAMENT_LEADERBOARD
    );

    public function __construct(DbTournamentLeaderboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTournamentLeaderboardPaginated($tournament, $limit = 50)
    {
        $leaderboard = $this->getCollection($this->cachePrefix . $tournament);

        $page = \Request::get('page', 0);

        return PaginatedEloquentResourceCollection::makeFromEloquentResourceCollection($leaderboard, $limit, $page);
    }

    public function getTournamentLeaderboard($tournamentID, $limit = 50, $qualified = false)
    {
        return $this->repository->getTournamentLeaderboard($tournamentID, $limit, $qualified);
    }

    public function getTournamentLeaderboardCollection($tournamentID, $limit = 50, $qualified = false)
    {
        return $this->repository->getTournamentLeaderboardCollection($tournamentID, $limit, $qualified);
    }

    public function getAllLeaderboardRecordsForTournament($tournament)
    {
        return $this->repository->getAllLeaderboardRecordsForTournament($tournament);
    }

    public function getLeaderboardRecordForUserInTournament($userId, $tournamentId)
    {
        return $this->repository->getLeaderboardRecordForUserInTournament($userId, $tournamentId);
    }

    public function updateLeaderboardRecordForUserInTournament($userId, $tournamentId, $turnover, $currency)
    {
        return $this->repository->updateLeaderboardRecordForUserInTournament($userId, $tournamentId, $turnover, $currency);
    }

    public function getLeaderBoardPositionForUser($userId, $tournamentId, $qualified = true)
    {
        return $this->repository->getLeaderBoardPositionForUser($userId, $tournamentId, $qualified);
    }

    public function getLeaderboardRecordsForTournamentWithCurrencyGreaterThen($tournamentId, $currency, $onlyQualified = true)
    {
        return $this->repository->getLeaderboardRecordsForTournamentWithCurrencyGreaterThen($tournamentId, $currency, $onlyQualified);
    }


    public function addToCollection($resource, $collectionKey, $resourceClass = null)
    {
        $key = $this->getCollectionCacheKey($collectionKey, $resource);

        $leaderboard = $this->getCollection($key);

        if (!$leaderboard) {
            $leaderboard = new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        if($leaderboardRecord = $leaderboard->get($key)) {
            $leaderboard->forget($key);
        }

        $leaderboard = $this->insertLeaderboardRecord($resource, $leaderboard);

        $this->put($key, $leaderboard->toArray(), $this->getCollectionCacheKey(self::COLLECTION_TOURNAMENT_LEADERBOARD, $resource));

    }

    /**
     * Simple insertion sort
     * @param $record \TopBetta\Resources\Tournaments\LeaderboardResource
     * @param $leaderboard EloquentResourceCollection
     * @return EloquentResourceCollection
     */
    protected function insertLeaderboardRecord($record, $leaderboard)
    {
        if (!$record->qualified()) {
            $leaderboard->push($record);
            return $leaderboard;
        }

        $newLeaderboard = new EloquentResourceCollection(new Collection(), $this->resourceClass);

        $position = 0;
        $positionSum = 0;
        foreach ($leaderboard as $leaderboardRecord) {
            if ($record->compare($leaderboardRecord) == 0) {
                $position = $leaderboardRecord->getPosition();
            } else if ($leaderboard->compare($leaderboardRecord) > 0) {
                $record->setPosition($position ? : $leaderboardRecord->getPosition());
                $newLeaderboard->push($record);
                $positionSum = 1;
            }

            if ($leaderboardRecord->qualified()) {
                $leaderboardRecord->setPosition($leaderboardRecord->getPosition() + $positionSum);
            }
            $newLeaderboard->push($leaderboardRecord);
        }

        return $newLeaderboard;
    }

    public function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_TOURNAMENT_LEADERBOARD:
                return Carbon::createFromFormat('Y-m-d H:i:s', $model->getModel()->tournament->end_date)->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("Invalid collection key " . $collectionKey);
    }

    public function getCollectionCacheKey($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_TOURNAMENT_LEADERBOARD:
                return $this->cachePrefix . $model->getModel()->tournament_id;
        }

        throw new \InvalidArgumentException("Invalid collection key " . $collectionKey);
    }
}