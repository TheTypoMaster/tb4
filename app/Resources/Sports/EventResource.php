<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 1:03 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class EventResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\EventModel';

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'display_flag' => 'display_flag',
        'start_date' => 'start_date',
    );

    protected $loadIfRelationExists = array(
        "teams" => "teams",
    );

    public function markets()
    {
        return $this->collection('markets', 'TopBetta\Resources\MarketResource', 'markets');
    }

    public function teams()
    {
        return $this->collection('teams', 'TopBetta\Resources\Sports\TeamResource', 'teams');
    }

    public function initialize()
    {
        parent::initialize();

        if (!array_get($this->relations, 'teams')) {
            $this->model->load('teams');
            $this->loadRelation('teams');
        }
    }
}