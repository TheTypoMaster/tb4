<?php namespace TopBetta\Resources\Sports;
/**
 * Created by PhpStorm.
 * User: Oliver Shanahan
 * Date: 24/11/2015
 * Time: 9:20 AM
 */

use TopBetta\Resources\AbstractEloquentResource;

class MarketTypeGroupResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\MarketTypeGroupModel';

    protected $attributes = array(
        'id' => 'market_type_group_id',
        'name' => 'market_type_group_name',
        'description' => 'market_type_group_description',
        "display_flag" => "market_type_group_display_flag",
        'icon' => 'icon',

    );

    public function icon()
    {
        return $this->model->icon ? $this->model->icon->icon_url : null;
    }
}