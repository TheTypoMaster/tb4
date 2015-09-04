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

    public function __construct(BaseCompetitionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}