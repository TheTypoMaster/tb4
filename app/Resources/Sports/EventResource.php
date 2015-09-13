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
        "display_flag" => "display_flag",
    );

    public function markets()
    {
        return $this->collection('markets', 'TopBetta\Resources\MarketResource', 'markets');
    }
}