<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 1:07 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class MarketTypeRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'market_types_';

    protected $resourceClass = 'TopBetta\Resources\Sports\MarketTypeResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array('sports', 'marketTypes');

    protected $storeIndividual = false;


    public function __construct(MarketTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMarketTypesForCompetition($competition)
    {
        return $this->getCollection($this->cachePrefix.'competition_'.$competition);
    }

    public function addMarketTypeToCompetition($competition, $marketType)
    {
        $collection = $this->getMarketTypesForCompetition($competition->id);

        if (!$collection) {
            $collection = new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        $collection->put($marketType->id, $this->createResource($marketType));

        Cache::tags($this->tags)->put($this->cachePrefix.'competition_'.$competition->id, $collection->toArray(), $this->getCompetitionCacheTime($competition));
    }

    public function storeMarketTypesForCompetition($types, $competition)
    {
        $marketTypes = new EloquentResourceCollection($types, $this->resourceClass);

        \Cache::tags($this->tags)->put($this->cachePrefix.'competition_'.$competition->id, $marketTypes->toArray(), $this->getCompetitionCacheTime($competition));
    }

    protected function getCompetitionCacheTime($model)
    {
        if (!$date=$model->close_time) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }

}