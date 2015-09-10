<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/07/2015
 * Time: 12:41 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class PlayerResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\PlayerModel';

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'icon' => 'icon',
        "display_flag" => "display_flag",
        "teamId" => "teamId",
    );

    public function icon()
    {
        return $this->model->icon ? $this->model->icon->icon_url : null;
    }

    public function setTeamId($id)
    {
        $this->model->team_id = $id;
        return $this;
    }

    public function getTeamId()
    {
        return $this->model->team_id;
    }
}