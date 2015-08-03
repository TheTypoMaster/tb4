<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:40 AM
 */

namespace TopBetta\Resources;


class SelectionResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id'         => 'id',
        'name'       => 'name',
        'number'     => 'number',
        'jockey'     => 'associate',
        'barrier'    => 'barrier',
        'handicap'   => 'handicap',
        'weight'     => 'weight',
        'winOdds'   => 'winOdds',
        'placeOdds' => 'placeOdds',
        'silk_id'    => 'silk_id',
    );

    protected $loadIfRelationExists = array(
        'runner' => 'runner'
    );

    protected $loadRelations = array(
        'result',
        'price',
        'runner',
        'runner.owner',
        'runner.trainer',
        'form',
        'lastStarts'
    );

    public function __construct($model)
    {
        $model->load($this->loadRelations);

        parent::__construct($model);

    }

    public function runner()
    {
        return $this->item('runner', 'TopBetta\Resources\RunnerResource', $this->model->runner);
    }

    public function getWinOdds()
    {
        $price = $this->model->price ? $this->model->price->win_odds : 0;

        return $price >= 1 ? $price : null;
    }

    public function getPlaceOdds()
    {
        $price = $this->model->price ? $this->model->price->place_odds : null;

        return $price >= 1 ? $price : null;
    }

    public function loadRelation($relation)
    {
        parent::loadRelation($relation);

        if( $relation == 'runner' ) {
            if( $this->model->form ) {
                $this->relations[$relation]->setForm($this->model->form);
            }

            if( $this->model->lastStarts ) {
                $this->relations[$relation]->setLastStarts($this->model->lastStarts);
            }
        }

        return $this->relations[$relation];
    }
}