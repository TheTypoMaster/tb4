<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/09/2015
 * Time: 4:52 PM
 */

namespace TopBetta\Repositories\Cache;

use Carbon\Carbon;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;

class MeetingRepository extends CachedResourceRepository {

    const COLLECTION_KEY_MEETING_DATE = 0;

    const CACHE_KEY_PREFIX = 'meetings_';

    protected $collectionKeys = array(
        self::COLLECTION_KEY_MEETING_DATE
    );

    protected $resourceClass = 'TopBetta\Resources\MeetingResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    public function __construct(CompetitionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMeetingsForDate(Carbon $date)
    {
        return $this->get($this->cachePrefix . $date->toDateString());
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch($keyTemplate) {
            case self::COLLECTION_KEY_MEETING_DATE:
                if (!$model->start_date){
                    return null;
                }
                return 'meetings_' . Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date)->toDateString();
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_KEY_MEETING_DATE:
                if (!$date=$model->start_date) {
                    $date = Carbon::now()->toDateTimeString();
                }
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getModelCacheTime($model)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date)->startOfDay()->addDays(2)->diffInMinutes();
    }

   protected function getModelKey($model)
   {
       return self::CACHE_KEY_PREFIX . '_' . $model->id;
   }
}