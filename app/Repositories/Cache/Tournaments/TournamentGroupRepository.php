<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/09/2015
 * Time: 10:25 AM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Repositories\DbTournamentGroupRepository;
use TopBetta\Repositories\DbTournamentRepository;
use TopBetta\Repositories\TournamentEventGroupRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\Tournaments\TournamentResource;

class TournamentGroupRepository extends CachedResourceRepository implements TournamentGroupRepositoryInterface {

    const CACHE_KEY_PREFIX = 'tournament_groups_';

    const COLLECTION_ALL_TOURNAMENT_GROUPS = 0;

    protected $resourceClass = 'TopBetta\Resources\Tournaments\TournamentGroupResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament_groups");

    protected $storeIndividualResource = false;

    protected $cacheForever = true;

    protected $collectionKey = array(
        self::COLLECTION_ALL_TOURNAMENT_GROUPS,
    );

    public function __construct(DbTournamentGroupRepository $repository,
                                DbTournamentRepository $tournamentRepository,
                                TournamentEventGroupRepository $tournamentEventGroupRepository)
    {
        $this->repository = $repository;
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentEventGroupRepository = $tournamentEventGroupRepository;
    }

    /**
     * get sport event groups with nested tournaments
     * @param Carbon|null $date
     * @return EloquentResourceCollection
     */
    public function getVisibleSportTournamentGroupsWithTournaments(Carbon $date = null)
    {

        $groups = $this->tournamentEventGroupRepository->getEventGroupsWithoutPaginate();
        $group_list = array();
        foreach($groups as $group) {
            $tournaments = $this->tournamentRepository->getTournamentWithEventGroupCollection($group->id);
            if(count($tournaments) > 0 && $group->type == 'sport') {
                $group_list[] = $group;
            }
        }

        $groups_resources = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($group_list), 'TopBetta\Resources\Tournaments\TournamentEventGroupResource');
        $groups_with_tournaments = array();

        foreach($groups_resources as $key => $group) {

            $tournaments = $this->tournamentRepository->getTournamentWithEventGroupCollection($group->id);

            if(count($tournaments) > 0 && $group->type == 'sport') {
                $groups_with_tournaments[] = $tournaments;
                $tournaments_resource = new EloquentResourceCollection($tournaments, 'TopBetta\Resources\Tournaments\TournamentResource');
                $group->setRelation('tournaments', $tournaments_resource);
            }

            if(count($tournaments) == 0) {
//                dd('group');
            }

        }
        return $groups_resources;

    }

    /**
     * get race event groups with nested tournaments
     * @param Carbon|null $date
     * @return EloquentResourceCollection
     */
    public function getVisibleRacingTournamentGroupsWithTournaments(Carbon $date = null)
    {

        $groups = $this->tournamentEventGroupRepository->getEventGroupsWithoutPaginate();
        $group_list = array();
        foreach($groups as $group) {
            $tournaments = $this->tournamentRepository->getTournamentWithEventGroupCollection($group->id);
            if(count($tournaments) > 0 && $group->type == 'race') {
                $group_list[] = $group;
            }
        }

        $groups_resources = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($group_list), 'TopBetta\Resources\Tournaments\TournamentEventGroupResource');

        $groups_with_tournaments = array();

        foreach($groups_resources as $key => $group) {

            $tournaments = $this->tournamentRepository->getTournamentWithEventGroupCollection($group->id);

            if(count($tournaments) > 0 && $group->type == 'race') {
                $groups_with_tournaments[] = $tournaments;
                $tournaments_resource = new EloquentResourceCollection($tournaments, 'TopBetta\Resources\Tournaments\TournamentResource');
                $group->setRelation('tournaments', $tournaments_resource);
            }

        }
        return $groups_resources;
    }

    /**
     * get all event groups with nested tournaments
     * @param Carbon|null $date
     * @return EloquentResourceCollection
     */
    public function getAllVisibleTournamentGroupsWithTournaments(Carbon $date = null)
    {
        $groups = $this->tournamentEventGroupRepository->getEventGroupsWithoutPaginate();
        $group_list = array();
        foreach($groups as $group) {
            $tournaments = $this->tournamentRepository->getTournamentWithEventGroupCollection($group->id);
            if(count($tournaments) > 0) {
                $group_list[] = $group;
            }
        }

        $groups_resources = new EloquentResourceCollection(new \Illuminate\Database\Eloquent\Collection($group_list), 'TopBetta\Resources\Tournaments\TournamentEventGroupResource');

        $groups_with_tournaments = array();

        foreach($groups_resources as $key => $group) {

            $tournaments = $this->tournamentRepository->getTournamentWithEventGroupCollection($group->id);

            if(count($tournaments) > 0) {
                $groups_with_tournaments[] = $tournaments;
                $tournaments_resource = new EloquentResourceCollection($tournaments, 'TopBetta\Resources\Tournaments\TournamentResource');
                $group->setRelation('tournaments', $tournaments_resource);
            }

        }
        return $groups_resources;

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
        $changes = $this->repository->addTournamentToGroups($tournament, $groups);
        if (count(array_get($changes, 'detached'))) {
            $this->removeTournamentFromGroupsArray($tournament, $changes['detatched']);
        }

        if ($tournament->start_date >= Carbon::now()->startofDay()) {
            $tournament->load('groups');
            $this->updateTournamentResource(new TournamentResource($tournament));
        }
    }

    public function updateTournamentResource($tournament)
    {
        $existingGroups = $this->getTournamentGroups();

        foreach ($tournament->getModel()->groups as $group) {
            $existingGroup = $existingGroups->get($group->id);

            if (!$existingGroup) {
                $existingGroup = $this->createResource($group);
                $existingGroup->setRelation('tournaments', new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Tournaments\TournamentResource'));
                $existingGroups->put($group->id, $existingGroup);
            }
            $existingGroup->setRelation('tournaments', $existingGroup->tournaments->keyBy('id'));
            $existingGroup->tournaments->put($tournament->id, $tournament);
        }

        $this->put($this->cachePrefix . 'all', $existingGroups->toArray(), null);
    }

    public function removeTournamentFromGroups($tournament)
    {
        $existingGroups = $this->getTournamentGroups();

        foreach ($tournament->groups as $group) {
            $existingGroup = $existingGroups->get($group->id);

            if ($existingGroup) {
                $existingGroup->setRelation(
                    'tournaments',
                    $existingGroup->tournaments->keyBy('id')->forget($tournament->id)
                );
            }

            if ($existingGroup->count()) {
                $existingGroups->put($group->id, $existingGroup);
            } else {
                $existingGroups->forget($group->id);
            }
        }

        $this->put($this->cachePrefix . 'all', $existingGroups->toArray(), null);
    }

    public function removeTournamentFromGroupsArray($tournament, $groupIds)
    {
        $existingGroups = $this->getTournamentGroups();

        foreach ($groupIds as $group) {
            $existingGroup = $existingGroups->get($group);

            if ($existingGroup) {
                $existingGroup->setRelation(
                    'tournaments',
                    $existingGroup->tournaments->keyBy('id')->forget($tournament->id)
                );
            }

            if ($existingGroup->count()) {
                $existingGroups->put($group, $existingGroup);
            } else {
                $existingGroups->forget($group);
            }

        }

        $this->put($this->cachePrefix . 'all', $existingGroups->toArray(), null);
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