<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 5:01 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class CompetitionResource extends AbstractEloquentResource {

    protected $attributes = array(
        "id" => "id",
        "name" => "name",
        "startDate" => "start_date",
        "icon" => "icon",
    );

    public function events()
    {
        return $this->collection('events', 'TopBetta\Resources\Sports\EventResource', $this->model->event);
    }

    public function icon()
    {
        if( $this->model->icon ) {
            return $this->model->icon;
        }

        return $this->model->baseCompetition->defaultEventGroupIcon ?
            $this->model->baseCompetition->defaultEventGroupIcon->icon_url :
            null;
    }
}