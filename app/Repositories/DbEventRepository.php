<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 13:19
 * Project: tb4
 */

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Models\Events;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Traits\SportsResourceRepositoryTrait;

class DbEventRepository extends BaseEloquentRepository implements EventRepositoryInterface{

    use SportsResourceRepositoryTrait;

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
        return $this->model->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_event_status', 'tbdb_event_status.id', '=', 'tbdb_event.event_status_id')
            ->select('tbdb_event.*', 'tbdb_event_group.name as competition_name', 'tbdb_event_status.name as event_status_name')
            ->orderBy('tbdb_event.start_date', 'DESC')
            ->where('tbdb_event.name', 'LIKE', "%$search%")
            ->orWhere('tbdb_event_group.name', 'LIKE', "%$search%")
            ->paginate();
    }

    /**
     * @return mixed
     */
    public function allEvents()
    {
        return $this->model->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_event_status', 'tbdb_event_status.id', '=', 'tbdb_event.event_status_id')
            ->select('tbdb_event.*', 'tbdb_event_group.name as competition_name', 'tbdb_event_status.name as event_status_name')
            ->orderBy('tbdb_event.start_date', 'DESC')
            ->paginate();
    }

    /**
     * get all events start from today
     * @return mixed
     */
    public function getAllEventsFromToday() {
        return $this->model->fromToday()->get();
    }


    public function getEventByEventID($id) {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Return the requested event deatils if it exists.
     *
     * @param $meetingId
     * @param $raceNo
     * @return mixed
     */
    public function getEventForMeetingIdRaceId($meetingId, $raceNo){
        $eventModel = $this->model->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
                            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                            ->where('tbdb_event_group.external_event_group_id', $meetingId )
                            ->where('tbdb_event.number', $raceNo)
                            ->select('tbdb_event.*')
                            ->first();
        if($eventModel) return $eventModel;

        return null;
    }


    public function getEventDetails($externalEventId){

        $eventDetails = $this->model->where('external_event_id',$externalEventId )
                                    ->select('id as EventId', 'external_event_id as ExternalEventId',
                                        'start_date as StartDate', 'event_status_id as EventStatusId')
                                    ->first();
        if($eventDetails){
            return $eventDetails->toArray();
        }
        return null;
    }

	public function getEventDetailByExternalId($externalEventId){

		$eventDetails = $this->model->where('external_event_id', $externalEventId)
								->first();
		if(!$eventDetails) return null;

		return $eventDetails->toArray();
	}

	public function getEventIdFromExternalId($externalEventId){
		$eventId = $this->model->where('external_event_id', $externalEventId)
								->value('id');
		if(!$eventId) return null;

		return $eventId;
	}

    public function getEventModelFromExternalId($externalEventId)
    {
        return $this->model->where('external_event_id', $externalEventId)
            ->first();
    }

    public function getEventWithStatusByEventId($eventId)
    {
        $eventDetails = $this->model->with('eventstatus')->where('id', $eventId)
                                    ->first();
        if (!$eventDetails) return null;

        return $eventDetails;
    }

    public function getEventsforCompetitionId($id, $from = null, $to = null){
        $query = $this->model->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
                                ->where('tbdb_event_group_event.event_group_id', $id);

        if($from) $query = $query->where('tbdb_event.start_date', '>', $from);
        if($to) $query = $query->where('tbdb_event.start_date', '<', $to);

        $events = $query->select(array('id as event_id', 'tbdb_event.name as event_name', 'tbdb_event.start_date as event_start_time'))
                        ->get();

        if(!$events) return null;

        return $events->toArray();
    }

    public function getEventsforDateRange($from, $to){
        $events = $this->model->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
                            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                            ->join('tbdb_tournament_sport', 'tbdb_tournament_sport.id', '=', 'tbdb_event_group.sport_id')
                            ->where('tbdb_event.start_date', '>', $from)
                            ->where('tbdb_event.start_date', '<', $to)
                            ->select(array('tbdb_event_group.id as competition_id', 'tbdb_event_group.name as competition_name',  'tbdb_tournament_sport.name as sport_name', 'tbdb_event.id as event_id', 'tbdb_event.name as event_name', 'tbdb_event.start_date as event_start_time'))
                            ->get();

        if(!$events) return null;

        return $events->toArray();
    }

    /**
     * adds and removes teams for an event based on the teams array
     * @param $eventId
     * @param array $teams (array of team ids and position [id => position]
     */
    public function addTeams($eventId, array $teams)
    {
        //get the event
        $event = $this->model->find($eventId);

        return $event->teams()->sync($teams);
    }

    public function addTeamsToModel($event, array $teams)
    {

        $currentTeams = $event->teams;

        $toAdd = array_diff(array_keys($teams), $currentTeams->lists('id')->all());

        $toAddTeams = array();

        foreach ($teams as $key=>$team) {
            if (in_array($key, $toAdd)) {
                $toAddTeams[$key] = $team;
            }
        }

        if (!count($toAddTeams)) {
            return $event;
        }

        return $event->teams()->attach($toAddTeams);
    }


    public function addToCompetition($eventId, $competitionId)
    {
        $event = $this->model->find($eventId);

        if (!$event->competitions()->find($competitionId)) {
            return $event->competitions()->attach($competitionId);
        }

        return null;
    }

    public function addEventModelToCompetition($event, $competition)
    {
        if (!$event->competitions()->find($competition->id)) {
            return $event->competitions()->attach($competition->id);
        }

        return null;
    }

    public function getNextToJumpSports($number = 10)
    {
        $builder = $this->getVisibleSportsEventBuilder();

        $model = $builder
            ->where('e.start_date', '>=', Carbon::now())
            ->groupBy('e.id')
            ->orderBy('e.start_date')
            ->take($number)
            ->get(array(
                'e.id as id',
                'e.name as name',
                'eg.id as competition_id',
                'eg.name as competition_name',
                'bc.id as base_competition_id',
                'bc.name as base_competition_name',
                'tb_sports.id as sport_id',
                'tb_sports.name as sport_name',
                'e.start_date as start_date',
            ));

        return $this->model->hydrate($model);
    }

    public function getVisibleEventsBetween($from, $to)
    {
        $builder = $this->getVisibleSportsEventBuilder();

        $model = $builder
            ->where('e.start_date', '>=', $from)
            ->where('e.start_date', '<=', $to)
            ->groupBy('e.id')
            ->orderBy('e.start_date')
            ->get(array('e.*'));

        return $this->model->hydrate($model);
    }

    public function getEventsForCompetition($competitionId)
    {
        $builder = $this->getVisibleSportsEventBuilder();

        $model = $builder
            ->where('ege.event_group_id', $competitionId)
            ->groupBy('e.id')
            ->orderBy('e.start_date')
            ->get(array('e.*'));

        return $this->model->hydrate($model);
    }

    public function addModelToCompetition($model, $competition)
    {
        if (!$model->competition->first()) {
            $model->competition()->attach($competition->id);

            //load the relationship
            $model->competition = new Collection();
            $model->competition->push($competition);
        }

        return $model;
    }

    public function getVisibleEVents()
    {
        $builder = $this->getVisibleSportsEventBuilder();

        $model = $builder
            ->where('e.start_date', '>=', Carbon::now())
            ->groupBy('e.id')
            ->orderBy('e.start_date')
            ->get(array('e.*'));

        return $this->model->hydrate($model);
    }


    public function addTeamPlayers($event, $team, $players)
    {
        $teamPlayers = $event->teamPlayers;

        $playersToInsert = array_diff($players, $teamPlayers->lists('player_id')->all());

        $playersToInsert = array_map(function ($v) use ($team, $event) {
            return array('player_id' => $v, "team_id" => $team, 'event_id' => $event->id, 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString());
        }, $playersToInsert);

        $event->teamPlayers()->insert($playersToInsert);

        return $this;
    }

    /**
     * get event group by event id
     * @param $event_id
     * @return mixed
     */
    public function getEventGroup($event_id) {
        return $this->model->find($event_id)
                           ->competitions()
                           ->first();
    }

    public function getEventsWithStatusIn($events, $statuses)
    {
        return $this->model
            ->whereIn('id', $events)
            ->whereIn('event_status_id', $statuses)
            ->get();
    }

    public function getBySerenaId($id)
    {
        return $this->model->where('serena_event_id', $id)->first();
    }

}