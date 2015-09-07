<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 4:57 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class SportResource extends IconResource {

    protected $attributes = array(
        "id" => "id",
        "name" => "name",
        "icon" => "icon",
    );

    public function baseCompetitions()
    {
        return $this->collection('baseCompetitions', 'TopBetta\Resources\Sports\BaseCompetitionResource', 'baseCompetitions');
    }

    public function loadIcon()
    {
        $this->icon = $this->model->icon ? $this->model->icon->icon_url : null;
        return $this;
    }

    public function addBaseCompetition($resource)
    {
        $baseCompetitions = $this->baseCompetitions()->keyBy('id');

        $baseCompetitions->put($resource->id, $resource);

        $this->relations['baseCompetitions'] = $baseCompetitions->values();
    }
}