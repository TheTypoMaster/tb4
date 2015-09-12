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
use TopBetta\Repositories\Traits\SportsResourceRepositoryTrait;

class DbCompetitionRepository extends BaseEloquentRepository implements CompetitionRepositoryInterface{
    use SportsResourceRepositoryTrait;

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
    public function search($search, $sportOnly = false)
    {
        $model = $this->model
            ->orderBy('start_date', 'DESC')
            ->where('name', 'LIKE', "%$search%");

        if( $sportOnly ) {
            $model->where('sport_id', '>', 3);
        }

        return $model->paginate();
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
        return $this->model->lists('name', 'id')->all();
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
            ->where('start_date', '>=', Carbon::now())
            ->where('display_flag', 1)
            ->orderBy('start_date', 'ASC')
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

    public function findAllSportsCompetitions($paged = null)
    {
        $model = $this->model
            ->where('sport_id', '>', 3)
            ->orderBy('start_date', 'DESC');

        if( $paged ) {
            return $model->paginate($paged);
        }

        return $model->get();
    }

    public function findBySport($sportId, $orderBy = array('name', 'ASC'), $paged = null)
    {
        $model = $this->model->where('sport_id', $sportId)->orderBy($orderBy[0], $orderBy[1]);

        if( $paged ) {
            return $model->paginate($paged);
        }

        return $model->get();
    }


    public function getRacingCompetitionsByDate(Carbon $date, $type = null, $withRaces = false)
    {
        $model = $this->model->where('sport_id', '<=', 3)
            ->where('start_date', '>=', $date->startOfDay()->toDateTimeString())
            ->where('start_date', '<=', $date->endOfDay()->toDateTimeString());

        if( $withRaces ) {
            $model->with(array(
                'competitionEvents',
                'competitionEvents.eventstatus'
            ));
        }


        if( $type ) {
            $model->where('type_code', $type);
        }

        return $model->get();
    }

    public function getByEvent($event)
    {
        return $this->model
            ->join('tbdb_event_group_event as ege', 'ege.event_group_id', '=', 'tbdb_event_group.id')
            ->where('ege.event_id', $event)
            ->first();
    }

    public function getVisibleCompetitionByBaseCompetition($baseCompetition)
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->where('eg.base_competition_id', $baseCompetition)
            ->where('e.start_date', '>=', Carbon::now())
            ->groupBy('eg.id')
            ->orderBy('start_date');

        return $this->model->hydrate($builder->get(array('eg.*')))->load(array('icon', 'baseCompetition.defaultEventGroupIcon'));
    }

    public function getVisibleCompetitions(Carbon $date = null)
    {
        $model = $this->model
            ->from('tbdb_event_group as eg')
            ->join('tb_base_competition as bc', 'bc.id', '=', 'eg.base_competition_id')
            ->join('tb_sports', 'tb_sports.id', '=', 'bc.sport_id')
            ->join('tbdb_event_group_event as ege', 'ege.event_group_id', '=', 'eg.id')
            ->join('tbdb_event as e', 'e.id', '=', 'ege.event_id')
            ->join('tbdb_market as m', 'm.event_id', '=', 'e.id')
            ->join('tbdb_selection as s', 's.market_id', '=', 'm.id')
            ->join('tbdb_selection_price as sp', 'sp.selection_id', '=', 's.id')
            ->where('tb_sports.display_flag', true)
            ->where('bc.display_flag', true)
            ->where('eg.display_flag', true)
            ->where('e.display_flag', true)
            ->where('m.display_flag', true)
            ->whereNotIn('m.market_status', array('D', 'S'))
            ->where(function($q) {
                $q
                    ->where(function($p) {
                        $p->where('sp.win_odds', '>', '1')->whereNull('sp.override_type');
                    })
                    ->orWhere(function($p) {
                        $p->where('sp.override_odds', '>', 1)->where('sp.override_type', '=', 'price');
                    })
                    ->orWhere(function($p) {
                        $p->where(\DB::raw('sp.override_odds * sp.win_odds'), '>', '1')->where('sp.override_type', 'percentage');
                    });
            })
            ->where('s.selection_status_id', 1)
            ->groupBy('eg.id')
            ->with(array('baseCompetition', 'baseCompetition.sport', 'icon', 'baseCompetition.defaultEventGroupIcon', 'baseCompetition.sport.icon', 'baseCompetition.icon', 'baseCompetition.sport.defaultCompetitionIcon'));

        if( $date ) {
            $model->where('e.start_date', '>=', $date->startOfDay()->toDateTimeString())->where('e.start_date', '<=', $date->endOfDay()->toDateTimeString());
        } else {
            $model->where('e.start_date', '>=', Carbon::now());
        }

        return $model->get(array('eg.*'));
    }

} 