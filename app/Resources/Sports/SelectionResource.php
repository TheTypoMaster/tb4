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
}