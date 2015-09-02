<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 12:32 PM
 */

namespace TopBetta\Repositories\Cache;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;

class RacingSelectionRepository extends CachedResourceRepository
{

    const COLLECTION_RACES_SELECTIONS = 0;

    protected $cachePrefix = 'selections_';

    protected $resourceClass = 'TopBetta\Resources\SelectionResource';

    protected $storeIndividualResource = false;

    protected $collectionKeys = array(
        self::COLLECTION_RACES_SELECTIONS,
    );

    public function __construct(SelectionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getSelectionsForRace($raceId)
    {
        return $this->get($this->cachePrefix . '_race_' . $raceId);
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch ($keyTemplate) {
            case self::COLLECTION_RACES_SELECTIONS:
                return $this->cachePrefix . '_race_' . $model->getModel()->market->event_id;
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_RACES_SELECTIONS:
                return Carbon::createFromFormat('Y-m-d H:i:s', $model->market->event->start_date)->startOfDay()->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("invalid key");
    }
}