<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 1:14 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class MarketResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'line' => 'line',
        'status' => 'market_status'
    );

    protected $loadIfRelationExists = array(
        "selections" => "selections",
    );

    public function selections()
    {
        return $this->collection('selections', 'TopBetta\Resources\Sports\SelectionResource', 'selections');
    }

    public function getName()
    {
        return $this->model->name ? : $this->model->markettype->name;
    }
}