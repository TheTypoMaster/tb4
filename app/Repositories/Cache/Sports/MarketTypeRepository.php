<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/09/2015
 * Time: 1:07 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class MarketTypeRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'market_types_';

    protected $resourceClass = 'TopBetta\Resources\Sports\MarketTypeResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $marketTypesCollectionTags = array('competition', 'marketTypes');

    protected $storeIndividual = false;
    /**
     * @var
     */
    private $competitionRepository;

    public function __construct(MarketTypeRepositoryInterface $repository, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->repository = $repository;
        $this->competitionRepository = $competitionRepository;
    }

    public function getMarketTypesForCompetition($competition)
    {
        return Cache::tags($this->marketTypesCollectionTags)->get($this->cachePrefix . 'competition_' . $competition);
    }

    public function updateMarketTypes()
    {
        $competitions = $this->competitionRepository->getVisibleCompetitions();

        $marketTypes = $this->repository->getAvailableMarketTypesForCompetitions($competitions->lists('id')->all());

        $typesCollections = array();
        foreach ($marketTypes as $marketType) {
            if (!$collection = array_get($typesCollections, $marketType->competition_id)) {
                $collection = new EloquentResourceCollection(new Collection(), $this->resourceClass);
            }

            $collection->push($this->createResource($marketType));
            $typesCollections[$marketType->competition_id] = $collection;
        }

        foreach ($typesCollections as $key=>$collection) {
            Cache::tags($this->marketTypesCollectionTags)->forever($this->cachePrefix . 'competition_' . $key, $collection);
        }
    }
}