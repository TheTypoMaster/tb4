<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 5:00 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class BaseCompetitionResource extends AbstractEloquentResource {

    protected $attributes = array(
        "id" => "id",
        "name" => "name",
        "icon" =>  "icon"
    );

    public function competitions()
    {
        return $this->collection('competitions', 'TopBetta\Resources\Sports\CompetitionResource', $this->model->competitions);
    }

    public function icon()
    {
        if( $this->model->icon ) {
            return $this->model->icon->icon_url;
        }

        return $this->model->sport->defaultCompetitionIcon ? $this->model->sport->defaultCompetitionIcon->icon_url : null;
    }
}