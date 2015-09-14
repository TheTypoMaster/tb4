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

        $leaderboard = $this->insertLeaderboardRecord($resource, $leaderboard);

        $this->put($key, $leaderboard->toArray(), $this->getCollectionCacheKey(self::COLLECTION_TOURNAMENT_LEADERBOARD, $resource));

    }

    protected function getPosition($previousRecord, $record, $totalPosition = 1)
    {
        return $previousRecord ? $previousRecord->compare($record) == 0 ? $previousRecord->getPosition() : $previousRecord->getPosition() + $totalPosition : 1;
    }

    /**
     * Simple insertion sort
     * @param $record \TopBetta\Resources\Tournaments\LeaderboardResource
     * @param $leaderboard EloquentResourceCollection
     * @return EloquentResourceCollection
     */
    protected function insertLeaderboardRecord($record, $leaderboard)
    {
        $newLeaderboard = new EloquentResourceCollection(new Collection(), $this->resourceClass);

        $previousRecord = null;
        $inserted = false;
        $totalPosition = 1;
        foreach ($leaderboard as $key=>$leaderboardRecord) {

            if ($leaderboardRecord->id != $record->id) {
                if ($record->compare($leaderboardRecord) > 0 && !$inserted) {
                    $record->setPosition($this->getPosition($previousRecord, $record, $totalPosition));
                    $newLeaderboard->push($record);

                    if ($previousRecord && $previousRecord->compare($record) == 0) {
                        $totalPosition++;
                    } else {
                        $totalPosition = 1;
                    }

                    $previousRecord = $record;
                    $inserted = true;
                }

                if ($leaderboardRecord->qualified()) {
                    $leaderboardRecord->setPosition($this->getPosition($previousRecord, $record, $totalPosition));
                }

                $newLeaderboard->push($leaderboardRecord);

                if ($previousRecord && $previousRecord->compare($leaderboardRecord) == 0) {
                    $totalPosition++;
                } else {
                    $totalPosition = 1;
                }

                $previousRecord = $leaderboardRecord;
            }
        }

        if (!$record->qualified()) {
            $newLeaderboard->push($record);
        }

        return $newLeaderboard;
    }

    public function removeRecord($record, $leaderboard)
    {
        $recordKey = null;
        foreach ($leaderboard as $key=>$leaderboardRecord) {
            if ($leaderboardRecord->id == $record->id) {
                $recordKey = $key;
                break;
            }
        }

        $leaderboard->forget($recordKey);

        return $leaderboard;
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

    protected function createCollectionFromArray($array, $resource = null)
    {
        return EloquentResourceCollection::createFromArray($array, $resource ? : $this->resourceClass);
    }
}