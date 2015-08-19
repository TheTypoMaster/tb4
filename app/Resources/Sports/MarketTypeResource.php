<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/07/2015
 * Time: 2:20 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

class MarketTypeResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id' => 'id',
        'name' => 'name',
        'icon' => 'icon',
    );

    public function icon()
    {
        return $this->model->icon ? $this->model->icon->icon_url : null;
    }
}