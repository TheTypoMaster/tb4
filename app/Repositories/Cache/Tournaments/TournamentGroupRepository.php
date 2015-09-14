<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/09/2015
 * Time: 10:25 AM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Repositories\DbTournamentGroupRepository;

class TournamentGroupRepository extends CachedResourceRepository implements TournamentGroupRepositoryInterface {

    const CACHE_KEY_PREFIX = 'tournament_groups_';

    const COLLECTION_ALL_TOURNAMENT_GROUPS = 0;

    protected $resourceClass = 'TopBetta\Resources\Tournaments\TournamentGroupResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament_groups");

    protected $storeIndividualResource = false;

    protected $collectionKey = array(
        self::COLLECTION_ALL_TOURNAMENT_GROUPS,
    );

    public function __construct(DbTournamentGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getVisibleSportTournamentGroupsWithTournaments(Carbon $date = null)
    {
        return $this->getTournamentGroups()->filter(function ($v) {
            $v->setRelation('tournaments', $v->tournaments->filter(function($q) {
                return $q->type == 'sport';
            }));

            return $v->tournaments->count() > 0;
        });
    }

    public function getVisibleRacingTournamentGroupsWithTournaments(Carbon $date = null)
    {
        return $this->getTournamentGroups()->filter(function ($v) {
            $v->setRelation('tournaments', $v->tournaments->filter(function($q) {
                return $q->type == 'racing';
            }));

            return $v->tournaments->count() > 0;
        });
    }

    public function getByName($name)
    {
        return $this->repository->getByName($name);
    }

    public function search($term)
    {
        return $this->repository->search($term);
    }

    public function getTournamentGroups()
    {
        return $this->getCollection($this->cachePrefix . 'all');
    }

    public function addTournamentToGroups($tournament, $groups)
    {
        if ($tournament->groups->count()) {
            $this->removeTournamentFromGroups($tournament);
        }

        $this->repository->addTournamentToGroups($tournament, $groups);

        if ($tournament->start_date >= Carbon::now()) {
            $existingGroups = $this->getTournamentGroups();

            foreach ($tournament->groups as $group) {
                $existingGroup = $existingGroups->get($group->id);

                if (!$existingGroup) {
                    $existingGroup = $this->createResource($group);
                    $existingGroups->put($group->id, $existingGroup);
                }

                $existingGroup->tournaments->put($tournament->id, $tournament);
            }

            \Cache::tags($this->tags)->forever($this->cachePrefix . 'all', $existingGroups);
        }
    }

    public function removeTournamentFromGroups($tournament)
    {
        $existingGroups = $this->getTournamentGroups();

        foreach ($tournament->groups as $group) {
            $existingGroup = $existingGroups->get($group->id);

            if ($existingGroup) {
                $existingGroup->tournaments->forget($tournament->id);
            }
        }

        \Cache::tags($this->tags)->forever($this->cachePrefix . 'all', $existingGroups);
    }


    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_ALL_TOURNAMENT_GROUPS:
                return $this->cachePrefix . 'all';
        }

        throw new \InvalidArgumentException("Invalid key " . $keyTemplate);
    }
}