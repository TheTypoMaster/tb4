<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/08/2015
 * Time: 10:40 AM
 */

namespace TopBetta\Resources;


class PriceResource extends AbstractEloquentResource
{

    protected $attributes = array(
        "id"        => "id",
        "winOdds"   => "winOdds",
        "placeOdds" => "placeOdds",
        "productId" => "bet_product_id",
    );

    protected $types = array(
        "id"        => 'int',
        "winOdds"   => 'float',
        "placeOdds" => 'float',
        "productId" => 'int',
    );

    public function getWinOdds()
    {
        if ($this->model->override_type == 'percentage') {
            return bcmul(2 - $this->override_odds, $this->model->win_odds, 2);
        } else if ($this->model->override_type == 'promo') {
            return $this->model->override_odds;
        } else if ($this->model->override_type == 'price') {
            return min($this->model->win_odds, $this->model->override_odds);
        }

        return $this->model->win_odds;
    }

    public function getPlaceOdds()
    {
        if ($this->model->override_place_type == 'percentage') {
            return bcmul(2 - $this->override_place_odds, $this->model->place_odds, 2);
        } else if ($this->model->override_place_type == 'promo') {
            return $this->model->override_place_odds;
        } else if ($this->model->override_place_type == 'price') {
            return min($this->model->place_odds, $this->model->override_place_odds);
        }

        return $this->model->place_odds;
    }
}

