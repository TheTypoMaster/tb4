<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/09/2015
 * Time: 10:17 AM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\DbTournamentRepository;
use TopBetta\Services\Tournaments\TournamentResultService;

class TournamentRepository extends CachedResourceRepository implements TournamentRepositoryInterface {

    const CACHE_KEY_PREFIX = 'tournaments_';

    protected $resourceClass = 'TopBetta\Resources\Tournaments\TournamentResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament");
    /**
     * @var TournamentGroupRepository
     */
    private $tournamentGroupRepository;

    public function __construct(DbTournamentRepository $repository, TournamentGroupRepository $tournamentGroupRepository)
    {
        $this->repository = $repository;
        $this->tournamentGroupRepository = $tournamentGroupRepository;
    }

    public function updateTournamentByEventGroupId($eventGroupId, $closeDate)
    {
        return $this->repository->updateTournamentByEventGroupId($eventGroupId, $closeDate);
    }

    public function makeCacheResource($tournament)
    {
        $resource = $this->createResource($tournament);

        $this->put($this->cachePrefix . $tournament->id, $resource->toArray(), $this->getModelCacheTime($tournament));

        $this->tournamentGroupRepository->updateTournamentResource($resource);

        return $tournament;
    }

    public function getTournament($id)
    {
        $tournament = $this->get($this->cachePrefix . $id);

        if (!$tournament) {
            return $this->repository->find($id);
        }

        return $tournament;
    }

    public function search($search)
    {
        return $this->repository->search($search);
    }

    public function tournamentOfTheDay($todVenue, $day = null)
    {
        return $this->repository->tournamentOfTheDay($todVenue, $day = null);
    }

    public function findCurrentJackpotTournamentsByType($type, $excludedTournaments = null)
    {
        return $this->repository->findCurrentJackpotTournamentsByType($type, $excludedTournaments = null);
    }

    public function getVisibleSportTournaments(Carbon $date = null)
    {
        return $this->repository->getVisibleSportTournaments($date);
    }

    public function getVisibleRacingTournaments(Carbon $date = null)
    {
        return $this->repository->getVisibleRacingTournaments($date);
    }

    public function getFinishedUnresultedTournaments()
    {
        return $this->repository->getFinishedUnresultedTournaments();
    }

    public function getUnresultedTournamentsByCompetition($competition)
    {
        return $this->repository->getUnresultedTournamentsByCompetition($competition);
    }

    public function getModelCacheTime($model)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $model->end_date)->addDays(2)->diffInMinutes();
    }
}