<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 4:19 PM
 */

namespace TopBetta\Repositories;


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
        $event = $this->model->findOrFail($eventId);

        $event->display_flag = $displayFlag;

        $event->save();

        return $event;
    }

    public function getAllSportEvents($paged = false)
    {
        $model = $this->model
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group_event.event_group_id', '=', 'tbdb_event_group.id')
            ->where('sport_id', '>', 3)
            ->orderBy('e.start_date', 'DESC');

        if( $paged ) {
            return $model->paginate($paged);
        }

        return $model->get();
    }
}