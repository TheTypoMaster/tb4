<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 10:25 AM
 */

namespace TopBetta\Services\Resources\Cache;

use App;
use Carbon\Carbon;
use TopBetta\Repositories\Cache\MeetingRepository;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Services\Resources\MeetingResourceService;

class CachedMeetingResourceService extends CachedResourceService {

    /**
     * @var MeetingRepository
     */
    private $meetingRepository;
    /**
     * @var CachedRaceResourceService
     */
    private $raceResourceService;

    public function __construct(MeetingResourceService $resourceService, MeetingRepository $meetingRepository, CachedRaceResourceService $raceResourceService)
    {
        $this->meetingRepository = $meetingRepository;
        $this->resourceService = $resourceService;
        $this->raceResourceService = $raceResourceService;
    }

    public function getSmallMeetings(Carbon $date)
    {
        $meetings = $this->meetingRepository->getSmallMeetings($date);

        if (!$meetings) {
            return $this->resourceService->getSmallMeetings($date, null, true);
        }

        return $meetings;
    }

    public function getMeetingsForDate($date, $type = null, $withRaces = false)
    {

        $meetings = $this->meetingRepository->getMeetingsForDate($date);

        if (!$meetings->count()) {
            return $this->resourceService->getMeetingsForDate($date, $type, $withRaces);
        }

        foreach ($meetings as $meeting) {
            $races = $this->raceResourceService->getRacesForMeeting($meeting->id);
            if ($withRaces) {
                $meeting->setRelation('races', $races);;
                $this->loadTotesForMeeting($meeting);
            }

            foreach ($races as $race) {
                if ($race->status == EventStatusRepositoryInterface::STATUS_SELLING) {
                    $meeting->setNextRaceDate($race->start_date);
                    $meeting->setNextRaceNumber($race->number);
                    break;
                }
            }
        }

        return $meetings;
    }

    public function getMeeting($id, $withRaces = false)
    {
        $model = $this->meetingRepository->getMeeting($id);

        if (!$model) {
            return $this->resourceService->getMeeting($id, $withRaces);
        }

        $races = $this->raceResourceService->getRacesForMeeting($model->id);

        foreach ($races as $race) {
            if ($race->status == EventStatusRepositoryInterface::STATUS_SELLING) {
                $model->setNextRaceDate($race->start_date);
                $model->setNextRaceNumber($race->number);
                break;
            }
        }

        if ($withRaces) {

            $model->setRelation('races', $races);

            $this->loadTotesForMeeting($model);
        }

        return $model;
    }
}