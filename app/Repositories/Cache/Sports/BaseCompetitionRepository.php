<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 5:08 PM
 */

namespace TopBetta\Repositories\Cache\Sports;


use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;

class BaseCompetitionRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'base_competitions_';

    protected $resourceClass = 'TopBetta\Resources\Sports\BaseCompetitionResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $cacheForever = true;

    protected $tags = array("sports", "baseCompetitions");

    /**
     * @var SportRepository
     */
    private $sportRepository;

    public function __construct(BaseCompetitionRepositoryInterface $repository, SportRepository $sportRepository)
    {
        $this->repository = $repository;
        $this->sportRepository = $sportRepository;
    }

    public function makeCacheResource($model)
    {
        parent::makeCacheResource($model);

        $resource = $this->createResource($model);

        $this->sportRepository->addBaseCompetition($resource);

        return $model;
    }
}