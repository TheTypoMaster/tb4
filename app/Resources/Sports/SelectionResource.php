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
        'price' => 'price',
        'won'   => 'won',
    );

    protected $types = array(
        "id" => "int",
        "line" => "int",
        "price" => "float",
        "won" => "bool",
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

    public function getWon()
    {
        return ! is_null($this->model->result);
    }

    public function getPrice()
    {
        if (!$this->model->price) {
            return 0;
        }

        if ($this->model->price->override_type == 'percentage') {
            return bcmul(2 - $this->model->price->override_odds, $this->model->price->win_odds, 2);
        } else if ($this->model->price->override_type == 'promo') {
            return $this->model->price->override_odds;
        } else if ($this->model->price->override_type == 'price') {
            return min($this->model->price->win_odds, $this->model->price->override_odds);
        }

        return $this->model->price->win_odds;
    }
}