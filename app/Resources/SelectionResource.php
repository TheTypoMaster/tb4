<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:40 AM
 */

namespace TopBetta\Resources;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class SelectionResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id'         => 'id',
        'name'       => 'name',
        'number'     => 'number',
        'jockey'     => 'associate',
        'barrier'    => 'barrier',
        'handicap'   => 'handicap',
        'weight'     => 'weight',
        'prices'     => 'prices',
        'silk_id'    => 'silk_id',
    );

    protected $loadIfRelationExists = array(
        'runner' => 'runner',
    );

    protected $loadRelations = array(
        'result',
        'price',
        'runner',
        'runner.owner',
        'runner.trainer',
        'form',
        'lastStarts'
    );

    private $products = null;

    public function __construct($model)
    {
        $model->load($this->loadRelations);

        parent::__construct($model);

    }

    public function runner()
    {
        return $this->item('runner', 'TopBetta\Resources\RunnerResource', $this->model->runner);
    }

    /**
     * Hacky way to set prices in products
     * @return mixed
     */
    public function prices()
    {
        return array(
            array(
                "product" => $this->products->get(BetTypeRepositoryInterface::TYPE_WIN)->productCode,
                "bet_type" => BetTypeRepositoryInterface::TYPE_WIN,
                "price" => $this->getWinOdds(),
            ),
            array(
                "product" => $this->products->get(BetTypeRepositoryInterface::TYPE_PLACE)->productCode,
                "bet_type" => BetTypeRepositoryInterface::TYPE_PLACE,
                "price" => $this->getPlaceOdds(),
            )
        );
    }

    public function setProducts($products)
    {
        $this->products = $products;
        $this->products->keyBy('betType');
    }

    public function getWinOdds()
    {
        $price = $this->model->price ? $this->model->price->win_odds : 0;

        return $price >= 1 ? $price : null;
    }

    public function getPlaceOdds()
    {
        $price = $this->model->price ? $this->model->price->place_odds : null;

        return $price >= 1 ? $price : null;
    }

    public function loadRelation($relation)
    {
        parent::loadRelation($relation);

        if( $relation == 'runner' ) {
            if( $this->model->form ) {
                $this->relations[$relation]->setForm($this->model->form);
            }

            if( $this->model->lastStarts ) {
                $this->relations[$relation]->setLastStarts($this->model->lastStarts);
            }
        }

        return $this->relations[$relation];
    }
}