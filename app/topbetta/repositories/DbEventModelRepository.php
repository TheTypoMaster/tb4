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
}