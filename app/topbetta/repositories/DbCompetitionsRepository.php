<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 12:19
 * Project: tb4
 */

use TopBetta\Models\CompetitionsModel;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;

class DbCompetitionsRepository extends BaseEloquentRepository implements CompetitionRepositoryInterface{

    protected $competitions;

    function __construct(CompetitionsModel $competitions) {
        $this->model = $competitions;
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
    public function allCompetitions()
    {
        return $this->model
            ->orderBy('start_date', 'DESC')
            ->paginate();
    }

    /**
     * get competition with provided external ID.
     *
     * @param $meetingId
     * @return mixed
     */
    public function getMeetingDetails($meetingId) {
        $racingCodes = array('R', 'G', 'H');

        $meetings =  RaceMeeting::where('external_event_group_id', '=', $meetingId)
                                ->whereIn('type_code', $racingCodes)->first();

        if($meetings){
            return $meetings->toArray();
        }
        return false;
    }
} 