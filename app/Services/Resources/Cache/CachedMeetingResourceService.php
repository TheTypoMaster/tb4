<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 10:25 AM
 */

namespace TopBetta\Services\Resources\Cache;

use App;
use TopBetta\Repositories\Cache\MeetingRepository;
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

    public function getMeetingsForDate($date, $type = null, $withRaces = false)
    {

        $meetings = $this->meetingRepository->getMeetingsForDate($date);

        if (!$meetings) {
            return $this->resourceService->getMeetingsForDate($date, $type, $withRaces);
        }

        if ($withRaces) {
            foreach ($meetings as $meeting) {

                $meeting->setRelation('races', $this->raceResourceService->getRacesForMeeting($meeting->id));
                $this->loadTotesForMeeting($meeting);
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

        if ($withRaces) {

            $model->setRelation('races', $this->raceResourceService->getRacesForMeeting($model->id));

            $this->loadTotesForMeeting($model);
        }

        return $model;
    }
}