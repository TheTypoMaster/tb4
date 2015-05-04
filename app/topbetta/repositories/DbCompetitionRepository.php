<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 12:19
 * Project: tb4
 */

use TopBetta\Models\CompetitionModel;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;

class DbCompetitionRepository extends BaseEloquentRepository implements CompetitionRepositoryInterface{

    protected $competitions;

    function __construct(CompetitionModel $competitions) {
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

        $meetings =  $this->model->where('external_event_group_id', '=', $meetingId)
                                ->whereIn('type_code', $racingCodes)->first();

        if($meetings){
            return $meetings->toArray();
        }
        return null;
    }

    public function selectList(){
        return $this->model->lists('name', 'id');
    }

    public function getDisplayedEventsForCompetition($competitionId)
    {
        return $this->model->find($competitionId)->events()->where("display_flag", "=", "1")->get();
    }

    public function setDisplayFlagForCompetition($competitionId, $displayFlag)
    {
        $competition = $this->model->find($competitionId);

        $competition->display_flag = $displayFlag;

        $competition->save();

        return $competition;
    }

    public function getCompetitionByExternalId($externalId)
    {
        return $this->model->where('external_event_group_id', $externalId)->first();
    }

    public function findByName($name)
    {
        return $this->model->where('name', $name)->first();
    }


    public function competitionFeed($input){


        $query = $this->model->join('tbdb_tournament_sport', 'tbdb_tournament_sport.id', '=', 'tbdb_event_group.sport_id');

        if(isset($input['sport'])){
            $query = $query->where('tbdb_tournament_sport.name', $input['sport']);
        }else{
            $query = $query->where('tbdb_event_group.sport_id', '!=', 0);
        }

        $competitions = $query->where('tbdb_event_group.display_flag', 1)
                                ->where('tbdb_tournament_sport.status_flag', 1)
                                ->select(array('tbdb_event_group.id as competition_id',  'tbdb_tournament_sport.name as competition_sport',
                                    'tbdb_event_group.name as competition_name', 'start_date as competition_start_date'))
                                ->get();

        if(!$competitions) return null;

        return $competitions->toArray();
    }
} 