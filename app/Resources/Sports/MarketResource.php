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

    protected static $modelClass = 'TopBetta\Models\MarketModel';

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'line' => 'line',
        'market_status' => 'market_status',
        "display_flag" => "display_flag",
        "market_type_id" => "market_type_id",
        'event_id' => 'event_id'
    );

    protected $types = array(
        "id" => "int",
        "display_flag" => "int",
        "market_type_id" => "int",
        'event_id' => 'int',
    );

    protected $loadIfRelationExists = array(
        "selections" => "selections",
        "markettype.markettypegroup" => "markettypegroup",
        "races" => "races",     "selections" => "selections",
        
    );

    public function selections()
    {
        return $this->collection('selections', 'TopBetta\Resources\Sports\SelectionResource', 'selections');
    }

    public function markettypegroup()
    {
        return $this->item('markettypegroup', 'TopBetta\Resources\Sports\MarketTypeGroupResource', 'markettype.markettypegroup');
    }

    public function getName()
    {
        return $this->model->name ? : $this->model->markettype->name;
    }

    public function addSelection($selection)
    {
        $selections = $this->selections->keyBy('id');

        $selections->put($selection->id, $selection);

        $this->relations['selections'] = $selections->values();
    }
}