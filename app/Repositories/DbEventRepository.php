<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 13:19
 * Project: tb4
 */

use Carbon\Carbon;
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

    public function addToCompetition($eventId, $competitionId)
    {
        $event = $this->model->find($eventId);

        if (!$event->competitions()->find($competitionId)) {
            return $event->competitions()->attach($competitionId);
        }

        return null;
    }

    public function getNextToJumpSports($number = 10)
    {
        $builder = $this->getVisibleSportsEventBuilder();

        $model = $builder
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

    protected function getVisibleSportsEventBuilder(Carbon $date = null)
    {
        $builder =  \DB::table('tbdb_event as e')
            ->join('tbdb_event_group_event as ege', 'ege.event_id', '=', 'e.id')
            ->join('tbdb_event_group as eg', 'eg.id', '=', 'ege.event_group_id')
            ->join('tb_base_competition as bc', 'bc.id', '=', 'eg.base_competition_id')
            ->join('tb_sports', 'tb_sports.id', '=', 'bc.sport_id')
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
            ->where('s.selection_status_id', 1);

        if( $date ) {
            $builder->where('eg.start_date', '>=', $date->startOfDay()->toDateTimeString())->where('eg.start_date', '<=', $date->endOfDay()->toDateTimeString());
        } else {
            $builder->where('eg.start_date', '>=', Carbon::now());
        }

        return $builder;
    }


}