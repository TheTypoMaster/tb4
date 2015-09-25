<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/09/2015
 * Time: 12:36 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class TournamentEventResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\EventModel';

    protected $attributes = array(
        "id"                => 'id',
        "event_group_id"    => "event_group_id",
        "type"              => "type",
        "start_date"        => 'start_date',
        "status"            => 'eventstatus.keyword',
    );


    protected function initialize()
    {
        parent::initialize();

        $this->model->event_group_id = $this->model->competition->first()->id;
        $this->model->type = $this->model->competition->first()->sport_id <= 3 ? 'racing' : 'sport';
    }
}