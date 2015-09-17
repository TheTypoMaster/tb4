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
use TopBetta\Resources\SmallMeetingResource;

class MeetingRepository extends CachedResourceRepository {

    const COLLECTION_KEY_MEETING_DATE = 0;
    const COLLECTION_SMALL_MEETINGS_RACES_DATE = 1;

    const CACHE_KEY_PREFIX = 'meetings_';

    protected $collectionKeys = array(
        self::COLLECTION_KEY_MEETING_DATE,
    );

    protected $resourceClass = 'TopBetta\Resources\MeetingResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("racing", "meetings");

    public function __construct(CompetitionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMeetingsForDate(Carbon $date)
    {
        return $this->getCollection($this->cachePrefix . $date->toDateString());
    }

    public function getMeeting($id)
    {
        return $this->get($this->cachePrefix . $id);
    }

    public function getSmallMeetings(Carbon $date)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . 'small_' . $date->toDateString());
    }

    public function getSmallMeetingsCollection(Carbon $date)
    {
        return $this->getCollection($this->cachePrefix . 'small_' . $date->toDateString());
    }

    public function makeCacheResource($model)
    {
        $model = parent::makeCacheResource($model);

        if ($model->start_date) {
            $resource = $this->createSmallMeeting($model);

            $meetings = $this->getSmallMeetingsCollection(Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date));

            if ($meetings && $meeting = $meetings->get($model->id)) {
                $resource->setRelation('races', $meeting->races);
            }

            \Log::debug("MeetingRepository (makeCacheResource): Adding small meetings " . $model->start_date . " count " . $meetings->count());
            $this->addToCollection($resource, self::COLLECTION_SMALL_MEETINGS_RACES_DATE, 'TopBetta\Resources\SmallMeetingResource');
        }

        return $model;
    }

    public function addSmallRace($resource, $meetingModel)
    {
        $meetings = $this->getSmallMeetingsCollection(Carbon::createFromFormat('Y-m-d H:i:s', $meetingModel->start_date));

        //check meeting exists
        if ($meetings && $meeting = $meetings->get($meetingModel->id)) {

            $races = $meeting->races->keyBy('id');

            $races->put($resource->id, $resource);

            $meeting->setRelation('races', $races->values());

            $this->addToCollection($meeting, self::COLLECTION_SMALL_MEETINGS_RACES_DATE, 'TopBetta\Resources\SmallMeetingResource');
        }
    }

    protected function getCollectionCacheKey($keyTemplate, $model)
    {
        switch($keyTemplate) {
            case self::COLLECTION_KEY_MEETING_DATE:
                if (!$model->start_date){
                    return null;
                }
                return 'meetings_' . Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date)->toDateString();
            case self::COLLECTION_SMALL_MEETINGS_RACES_DATE:
                if (!$model->start_date){
                    return null;
                }
                return 'meetings_small_' . Carbon::createFromFormat('Y-m-d H:i:s', $model->start_date)->toDateString();
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_KEY_MEETING_DATE:
            case self::COLLECTION_SMALL_MEETINGS_RACES_DATE:
                if (!$date=$model->start_date) {
                    $date = Carbon::now()->toDateTimeString();
                }
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("invalid key");
    }

    protected function getModelCacheTime($model)
    {
        if (!$date=$model->start_date) {
            $date = Carbon::now()->toDateTimeString();
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->startOfDay()->addDays(2)->diffInMinutes();
    }

   protected function getModelKey($model)
   {
       return self::CACHE_KEY_PREFIX . '_' . $model->id;
   }

    protected function createSmallMeeting($model)
    {
        return new SmallMeetingResource($model);
    }
}