<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 12:19
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Models\CompetitionModel;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;

class DbCompetitionRepository extends BaseEloquentRepository implements CompetitionRepositoryInterface{

    protected $competitions;

    function __construct(CompetitionModel $competitions) {
        $this->model = $competitions;
    }
	/*
		 * Relationships
		 */
	public function events()
	{
		return $this->belongsToMany('TopBetta\Models\EventModel', 'tbdb_event_group_event', 'event_group_id', 'event_id');
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

	public function getMeetingFromExternalId($meetingId) {
		$meeting = $this->model->where('external_event_group_id', $meetingId)
						->first();
		if(!$meeting) return null;

		return $meeting->toArray();
	}

	public function getMeetingFromCode($meetingCode) {
		$meeting = $this->model->where('meeting_code', '=', $meetingCode)
					->first();
		if(!$meeting) return null;

		return $meeting->toArray();
	}

    public function getFutureEventGroupsByTournamentCompetition($tournamentCompetitionId)
    {
        return $this->model
            ->where('tournament_competition_id', $tournamentCompetitionId)
            ->whereHas('events', function($q){
                $q->where('start_date', '>=', Carbon::now()->toDateTimeString());
            })
            ->where('display_flag', 1)
            ->get();
    }

    public function getFirstEventForCompetition($competitionId)
    {
        return $this->model->find($competitionId)
            ->events()
            ->orderBy('start_date', 'ASC')
            ->first();
    }

    public function getLastEventForCompetition($competitionId)
    {
        return $this->model->find($competitionId)
            ->events()
            ->orderBy('start_date', 'DESC')
            ->first();
    }

	public function getCompetitionBySelection($selectionId)
    {
        return $this->model
            ->join('tbdb_event_group_event', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
            ->join('tbdb_market', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_selection', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
            ->where('tbdb_selection.id', $selectionId)
            ->firstOrFail();
    }

} 