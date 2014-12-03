<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 13:19
 * Project: tb4
 */

use TopBetta\Models\Events;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;

class DbEventRepository extends BaseEloquentRepository implements EventRepositoryInterface{

    protected $events;

    function __construct(Events $events) {
        $this->model = $events;
    }

    /**
     * @param $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->model
            ->orderBy('start_date', 'DESC')
            ->where('name', 'LIKE', "%$search%")
            ->paginate();
    }

    /**
     * @return mixed
     */
    public function allEvents()
    {
        return $this->model
            ->orderBy('start_date', 'DESC')
            ->paginate();
    }

    /**
     * Check if a event exists.
     *
     * @param $meetingId
     * @param $raceNo
     * @return mixed
     */
    public function getEventForMeetingIdRaceId($meetingId, $raceNo){
        return $this->model->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
                            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                            ->where('tbdb_event_group.external_event_group_id', $meetingId )
                            ->where('tbdb_event.number', $raceNo)
                            ->select('tbdb_event.*')
                            ->first();
    }


} 