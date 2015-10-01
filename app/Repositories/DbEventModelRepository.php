<?php namespace TopBetta\Repositories;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 4:19 PM
 */

use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Models\EventModel;
use \TopBetta\Repositories\Contracts\EventModelRepositoryInterface;

class DbEventModelRepository extends BaseEloquentRepository implements EventModelRepositoryInterface
{

    public function __construct(EventModel $event)
    {
        $this->model = $event;
    }


    public function setDisplayFlagForEvent($eventId, $displayFlag)
    {
        // $event = $this->model->findOrFail($eventId);

        $event = $this->model->where('external_event_id', $eventId)->first();

        if(!$event) Throw new ModelNotFoundException;

        $event->display_flag = $displayFlag;

        $event->save();

        return $event;
    }

    public function setFixedOddsFlagForEvent($eventId, $fixedOddsFlag)
    {
        // $event = $this->model->findOrFail($eventId);

        $event = $this->model->where('external_event_id', $eventId)->first();

        if(!$event) Throw new ModelNotFoundException;

        $event->fix_odds_enabled = $fixedOddsFlag;

        $event->save();

        return $event;
    }

    public function getAllSportEvents($paged = false)
    {
        $model = $this->model
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group_event.event_group_id', '=', 'tbdb_event_group.id')
            ->where('sport_id', '>', 3)
            ->orderBy('tbdb_event.start_date', 'DESC')
            ->select(array("tbdb_event.*"));

        if( $paged ) {
            return $model->paginate($paged);
        }

        return $model->get();
    }

    public function searchSportEvents($term, $paged = false)
    {
        $model = $this->model
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group_event.event_group_id', '=', 'tbdb_event_group.id')
            ->join('tbdb_tournament_sport', 'tbdb_tournament_sport.id', '=', 'tbdb_event_group.sport_id')
            ->where('sport_id', '>', 3)
            ->where(function($q) use ($term) {
                $q->where('tbdb_event.name', 'LIKE', "%$term%")
                    ->orWhere('tbdb_event_group.name', 'LIKE', "%$term%")
                    ->orWhere('tbdb_tournament_sport.name', 'LIKE', "%$term%");
            })
            ->orderBy('tbdb_event.start_date', 'DESC')
            ->select(array("tbdb_event.*"));

        if( $paged ) {
            return $model->paginate($paged);
        }

        return $model->get();
    }



    public function getEvent($eventId, $withSelections = false)
    {
        $model = $this->model->where('id', $eventId)->with('eventstatus');

        if( $withSelections ) {
            $model->with('markets.selections');
        }

        return $model->first();
    }

    public function getEventsForCompetition($competitionId)
    {

        return $this->model
            ->join('tbdb_event_group_event as ege', 'ege.event_id' , '=', 'tbdb_event.id')
            ->where('ege.event_group_id', $competitionId)
            ->with('eventstatus')
            ->get();
    }

    public function getByTournamentEventGroup($eventGroup)
    {
        return $this->model
            ->join('tb_tournament_event_group_event as ege', 'ege.event_id', '=', 'tbdb_event.id')
            ->where('ege.tournament_event_group_id', $eventGroup)
            ->with(array('competition', 'eventstatus'))
            ->get(array('tbdb_event.*'));
    }
}