<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/07/2015
 * Time: 12:41 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class TeamResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\TeamModel';

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'icon' => 'icon',
        "display_flag" => "display_flag",
    );

    public function icon()
    {
        return $this->model->icon ? $this->model->icon->icon_url : null;
    }

    public function getPositition()
    {
        if (!$this->model->position) {
            return object_get($this->model, 'pivot.team_position');
        }

        return $this->model->position;
    }

    public function toArray()
    {
        $array = parent::toArray();

        if ($position = $this->getPositition()) {
            $array['position'] = $position;
        }

        return $array;
    }

}