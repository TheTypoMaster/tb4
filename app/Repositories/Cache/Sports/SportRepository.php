<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Repositories\Cache\Sports;

use Cache;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class SportRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'sports_';

    const COLLECTION_ALL_SPORTS = 0;

    protected $resourceClass = 'TopBetta\Resources\Sports\SportResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("sports", "sport");

    protected $allSportsCollectionKey;

    protected $collectionKeys = array(
        self::COLLECTION_ALL_SPORTS,
    );

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
        return $this->getCollection($this->allSportsCollectionKey);
    }

    public function addBaseCompetition($resource)
    {
        $sports = $this->getVisibleSportsAndBaseCompetitions();

        $sport = $sports->get($resource->sport_id);

        if ($sport) {
            $sport->addBaseCompetition($resource);
            $this->save($sport);
        }
    }

    protected function createResource($model)
    {
        $resource = parent::createResource($model);

        $resource->setRelation('baseCompetitions', new EloquentResourceCollection(new Collection(), 'TopBetta\Resource\Sports\BaseCompetitionResource'));

        return $resource;
    }


    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_ALL_SPORTS:
                return $this->cachePrefix . 'all';
        }

        throw new \InvalidArgumentException("Invalid key " . $keyTemplate);
    }


}