<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class SportRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'sports_';

    protected $resourceClass = 'TopBetta\Resources\Sports\SportResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $allSportsCollectionKey;

    protected $cacheForever = true;
    /**
     * @var BaseCompetitionRepositoryInterface
     */
    private $baseCompetitionRepository;

    public function __construct(SportRepositoryInterface $repository, BaseCompetitionRepositoryInterface $baseCompetitionRepository)
    {
        $this->repository = $repository;
        $this->baseCompetitionRepository = $baseCompetitionRepository;
        $this->allSportsCollectionKey = self::CACHE_KEY_PREFIX . 'all';
    }

    public function getVisibleSportsAndBaseCompetitions()
    {
        return $this->get($this->allSportsCollectionKey);
    }

    public function updateVisibleSportsAndBaseCompetitions()
    {
        $sports = $this->repository->getVisibleSportsAndBaseCompetitions();

        $baseCompetitions = $this->baseCompetitionRepository->findIn($sports->lists('base_competition_id')->all());

        $sports = new EloquentResourceCollection($sports->unique('id'), 'TopBetta\Resources\Sports\SportResource');
        $baseCompetitions = new EloquentResourceCollection($baseCompetitions, 'TopBetta\Resources\Sports\BaseCompetitionResource');

        $sports->setRelations('baseCompetitions', 'sport_id', $baseCompetitions);

        $this->put($this->allSportsCollectionKey, $sports, null);
    }

}