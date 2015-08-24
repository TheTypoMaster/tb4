<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/08/2015
 * Time: 3:41 PM
 */

namespace TopBetta\Resources;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class ProductResource extends AbstractEloquentResource {

    protected $attributes = array(
        "productId"   => "id",
        "betType"     => "bet_type",
        "productCode" => "product_code",
        "productName" => "name",
        "fixed"       => "is_fixed_odds",
    );

    protected $types = array(
        "id" => "int",
        "fixed" => "bool",
    );
}