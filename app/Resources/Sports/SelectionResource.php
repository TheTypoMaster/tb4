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

    protected static $modelClass = 'TopBetta\Models\SelectionModel';

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'line' => 'price.line',
        'price' => 'price',
        'won'   => 'won',
        "display_flag" => "display_flag",
        "selection_status_id" => "selection_status_id",
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
        return $this->item('team', 'TopBetta\Resources\Sports\TeamResource', 'team');
    }

    public function player()
    {
        return $this->item('player', 'TopBetta\Resources\Sports\PlayerResource', 'player');
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

        if (!is_object($this->model->price)) {
            return $this->model->price;
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