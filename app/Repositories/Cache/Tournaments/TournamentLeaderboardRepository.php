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
use TopBetta\Jobs\UpdateTournamentLeaderboard;
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
    /**
     * @var TournamentTicketRepository
     */
    private $ticketRepository;

    public function __construct(DbTournamentLeaderboardRepository $repository, TournamentTicketRepository $ticketRepository)
    {
        $this->repository = $repository;
        $this->ticketRepository = $ticketRepository;
    }

    public function getTournamentLeaderboardPaginated($tournament, $limit = 50)
    {
        $leaderboard = $this->getCollection($this->cachePrefix . $tournament);

        if (!$leaderboard->count()) {
            return $this->repository->getTournamentLeaderboardPaginated($tournament, $limit);
        }

        $page = \Request::get('page', 0);

        return PaginatedEloquentResourceCollection::makeFromEloquentResourceCollection($leaderboard, $limit, $page);
    }

    public function insertLeaderboard($tournament, $leaderboard)
    {
        $newLeaderboard = new EloquentResourceCollection($leaderboard, $this->resourceClass);
        $positionLeaderboard = new EloquentResourceCollection(new Collection(), $this->resourceClass);

        $position = 0;
        $positionCount = 1;
        $previousRecord = null;
        foreach ($newLeaderboard as $key=>$leaderboardRecord) {

            if ($previousRecord && $previousRecord->compare($leaderboardRecord) == 0) {
                $positionCount++;
            } else {
                $position += $positionCount;
                $positionCount = 1;
            }

            if ($leaderboardRecord->qualified()) {
                $leaderboardRecord->setPosition($position);
            }

            $positionLeaderboard->push($leaderboardRecord);
            $previousRecord = $leaderboardRecord;
        }

        $this->put(
            $this->cachePrefix . $tournament->id,
            $positionLeaderboard->toArray(),
            Carbon::createFromFormat('Y-m-d H:i:s', $tournament->end_date)->addDays(2)->diffInMinutes()
        );
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

    public function makeCacheResource($model)
    {
        \Bus::dispatch(new UpdateTournamentLeaderboard($model));

        return $model;
    }

    public function updateCacheLeaderboard($model)
    {
        $resource = $this->createResource($model);

        $this->addToCollection($resource, self::COLLECTION_TOURNAMENT_LEADERBOARD);
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

    public function delete($model)
    {
        $leaderboard = $this->getCollection($this->cachePrefix . $model->tournament_id);

        if ($leaderboard) {
            $leaderboard = $this->removeRecord($model, $leaderboard);

            $this->put(
                $this->cachePrefix . $model->tournament_id,
                $leaderboard->toArray(),
                Carbon::createFromFormat('Y-m-d H:i:s', $model->tournament->end_date)->addDays(2)->diffInMinutes()
            );
        }

        return parent::delete($model);
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
        $positionCount = 1;
        $position = 0;
        foreach ($leaderboard as $key=>$leaderboardRecord) {

            if ($leaderboardRecord->id != $record->id) {

                if ($record->compare($leaderboardRecord) > 0 && !$inserted) {

                    if ($previousRecord && $previousRecord->compare($leaderboardRecord) == 0) {
                        $positionCount++;
                    } else {
                        $position += $positionCount;
                        $positionCount = 1;
                    }

                    if ($record->qualified()) {
                        $record->setPosition($position);
                    }

                    $this->ticketRepository->updatePositionAndTurnOver($record->userId, $record->getModel()->tournament_id, $record->getPosition(), $record->turned_over, $record->balance_to_turnover);
                    $newLeaderboard->push($record);
                    $previousRecord = $record;
                    $inserted = true;
                }

                if ($previousRecord && $previousRecord->compare($leaderboardRecord) == 0) {
                    $positionCount++;
                } else {
                    $position += $positionCount;
                    $positionCount = 1;
                }

                if ($leaderboardRecord->qualified()) {
                    $leaderboardRecord->setPosition($position);
                }

                $this->ticketRepository->updatePosition($leaderboardRecord->userId, $record->getModel()->tournament->id, $leaderboardRecord->getPosition());
                $newLeaderboard->push($leaderboardRecord);
                $previousRecord = $leaderboardRecord;
            }
        }

        if (!$record->qualified()) {
            $newLeaderboard->push($record);
            $this->ticketRepository->updatePositionAndTurnover($record->userId, $record->getModel()->tournament_id, $record->getPosition(), $record->turned_over, $record->balance_to_turnover);
        }

        return $newLeaderboard;
    }

    public function removeRecord($record, $leaderboard)
    {
        $recordKey = null;

        $newLeaderboard = new EloquentResourceCollection(new Collection(), $this->resourceClass);

        $position = 0;
        $positionCount = 1;
        $previousRecord = null;
        foreach ($leaderboard as $key=>$leaderboardRecord) {
            if ($leaderboardRecord->id != $record->id) {

                if ($previousRecord && $previousRecord->compare($leaderboardRecord) == 0) {
                    $positionCount++;
                } else {
                    $position += $positionCount;
                    $positionCount = 1;
                }

                if ($leaderboardRecord->qualified()) {
                    $leaderboardRecord->setPosition($position);
                }

                $this->ticketRepository->updatePosition($leaderboardRecord->userId, $record->tournament->id, $leaderboardRecord->getPosition());
                $newLeaderboard->push($leaderboardRecord);
                $previousRecord = $leaderboardRecord;
            }
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

    protected function createCollectionFromArray($array, $resource = null)
    {
        return EloquentResourceCollection::createFromArray($array, $resource ? : $this->resourceClass);
    }
}