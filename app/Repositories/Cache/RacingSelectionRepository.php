<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 12:32 PM
 */

namespace TopBetta\Repositories\Cache;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class RacingSelectionRepository extends CachedResourceRepository
{
    protected static $modelClass = 'TopBetta\Models\SelectionModel';

    const COLLECTION_RACES_SELECTIONS = 0;

    protected $cachePrefix = 'selections_';

    protected $resourceClass = 'TopBetta\Resources\SelectionResource';

    protected $storeIndividualResource = false;

    protected $tags = array("racing", "selections");

    protected $collectionKeys = array(
        self::COLLECTION_RACES_SELECTIONS,
    );

    public function __construct(SelectionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getSelectionsForRace($raceId)
    {
        return $this->getCollection($this->cachePrefix . '_race_' . $raceId);
    }

    public function updatePricesForSelectionInRace($selectionId, $race, $price)
    {
        if ($selections = $this->getSelectionsForRace($race['id'])) {
            if ($selection = $selections->getDictionary()[$selectionId]) {
                $selection->addPrice($price);
                $selections->put($selection->id, $selection);
                \Cache::tags($this->tags)->put($this->cachePrefix . '_race_' . $race['id'], $selections->toArray(), $this->getRaceCollectionTime($race));
            }
        }
    }

    protected function getRaceCollectionTime($race)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $race['start_date'])->startOfDay()->addDays(2)->diffInMinutes();
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