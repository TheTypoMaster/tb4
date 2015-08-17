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
        "id"          => "id",
        "betType"     => "betType",
        "productCode" => "product_name",
    );

    public function getBetType()
    {
        switch ($this->model->bet_type) {
            case 'W':
                return BetTypeRepositoryInterface::TYPE_WIN;
            case 'P':
                return BetTypeRepositoryInterface::TYPE_PLACE;
            case 'Q':
                return BetTypeRepositoryInterface::TYPE_QUINELLA;
            case 'E':
                return BetTypeRepositoryInterface::TYPE_EXACTA;
            case 'T':
                return BetTypeRepositoryInterface::TYPE_TRIFECTA;
            case 'FF':
                return BetTypeRepositoryInterface::TYPE_FIRSTFOUR;
        }

        return null;
    }
}