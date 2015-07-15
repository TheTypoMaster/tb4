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
        "name" => "name"
    );

    public function competitions()
    {
        return $this->collection('competitions', 'TopBetta\Resources\Sports\CompetitionResource', $this->model->competitions);
    }
}