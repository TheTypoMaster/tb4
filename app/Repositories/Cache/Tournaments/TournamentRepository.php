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

class TournamentRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'tournaments_';

    protected $resourceClass = 'TopBetta\Resources\Tournaments\TournamentResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament");

    public function __construct(TournamentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getModelCacheTime($model)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $model->end_date)->addDays(2)->diffInMinutes();
    }
}