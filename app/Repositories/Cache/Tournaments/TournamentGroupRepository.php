<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/09/2015
 * Time: 10:25 AM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\DbTournamentGroupRepository;

class TournamentGroupRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'tournament_groups_';

    const COLLECTION_ALL_TOURNAMENT_GROUPS = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\SportResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament_groups");

    protected $storeIndividualResource = false;

    public function __construct(DbTournamentGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTournamentGroups()
    {
        return $this->getCollection($this->cachePrefix . 'all');
    }

    public function addTournamentToGroups($tournament, $groups)
    {
        $this->repository->addTournamentToGroups($tournament, $groups);

        $existingGroups = $this->getTournamentGroups();

        foreach ($tournament->groups as $group) {

        }
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