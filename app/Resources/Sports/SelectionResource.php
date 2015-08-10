<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 1:17 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class SelectionResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'line' => 'price.line',
        'price' => 'price.win_odds',
    );

    protected $types = array(
        "id" => "int",
        "line" => "int",
        "price" => "float"
    );

    protected $loadIfRelationExists = array(
        'team' => 'team',
        'player' => 'player',
    );

    public function team()
    {
        return $this->item('team', 'TopBetta\Resources\Sports\TeamResource', $this->model->team->first());
    }

    public function player()
    {
        return $this->item('player', 'TopBetta\Resources\Sports\PlayerResource', $this->model->player->first());
    }
}