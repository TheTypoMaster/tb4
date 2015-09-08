<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 5:01 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class CompetitionResource extends IconResource{

    protected static $modelClass = 'TopBetta\Models\CompetitionModel';

    protected $attributes = array(
        "id" => "id",
        "name" => "name",
        "startDate" => "start_date",
        "icon" => "icon",
        "display_flag" => "display_flag",
    );

    public function events()
    {
        return $this->collection('events', 'TopBetta\Resources\Sports\EventResource', 'events');
    }

    public function loadIcon()
    {
        if( $this->model->icon ) {
            $this->icon = $this->model->icon;
            return $this;
        }

        $this->icon = $this->model->baseCompetition->defaultEventGroupIcon ?
            $this->model->baseCompetition->defaultEventGroupIcon->icon_url :
            null;

        return $this;
    }
}