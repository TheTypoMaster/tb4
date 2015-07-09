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
        'win_odds'   => 'price.win_odds',
        'place_odds' => 'price.place_odds',
        'silk_id'    => 'silk_id',
    );

    protected $loadIfRelationExists = array(
        'runner' => 'runner'
    );

    public function runner()
    {
        return $this->item('runner', 'TopBetta\Resources\RunnerResource', $this->model->runner);
    }

    protected function loadRelation($relation)
    {
        parent::loadRelation($relation);

        if( $relation == 'runner' ) {
            $this->relations[$relation]->setForm($this->model->form);
            $this->relations[$relation]->setLastStarts($this->model->lastStarts);
        }

        return $this->relations[$relation];
    }
}