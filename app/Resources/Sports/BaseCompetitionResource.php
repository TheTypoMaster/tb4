<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 5:00 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class BaseCompetitionResource extends IconResource {

    protected static $modelClass = 'TopBetta\Models\BaseCompetitionModel';

    protected $attributes = array(
        "id" => "id",
        "name" => "name",
        "icon" =>  "icon",
        "display_flag" => "display_flag",
    );

    public function competitions()
    {
        return $this->collection('competitions', 'TopBetta\Resources\Sports\CompetitionResource', 'competitions');
    }

    public function loadIcon()
    {
        if( $this->model->icon ) {
            $this->icon = $this->model->icon->icon_url;
            return $this;
        }

        $this->icon = $this->model->sport->defaultCompetitionIcon ? $this->model->sport->defaultCompetitionIcon->icon_url : null;
        return $this;
    }
}