<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:21 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class LeaderboardResource extends AbstractEloquentResource {

    protected $attributes = array(
        "username" => "username",
        "currency" => 'currency',
        'turned_over' => 'turned_over',
        'rebuys' => 'rebuys',
        'topups' => 'topups',
        'qualified' => 'qualified',
    );

    private $position = '-';

    public function qualified()
    {
        return $this->model->turned_over >= $this->model->balance_to_turnover;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array['position'] = $this->position;

        return $array;
    }

}