<?php namespace TopBetta\Repositories\Cache\Sports;
/**
 * Created by PhpStorm.
 * User: Oliver Shanahan
 * Date: 24/09/2015
 * Time: 1:07 PM
 */

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
//use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeGroupRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class MarketTypeGroupRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'market_type_groups_';

    protected $resourceClass = 'TopBetta\Resources\Sports\MarketTypeGroupResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array('sports', 'MarketTypeGroups');

    protected $storeIndividual = false;


    public function __construct(MarketTypeGroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}