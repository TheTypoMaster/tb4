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
        'form'       => 'form',
        'deductions' => 'deductions',
    );

    protected $types = array(
        "id" => "int",
        "number" => "int",
        "silk_id" => "int",
        "barrier" => "int",
    );

    protected $loadIfRelationExists = array(
        'runner' => 'runner',
        'prices' => 'prices',
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
        return $this->collection('prices', 'TopBetta\Resources\PriceResource', $this->model->prices);
    }

    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function getForm()
    {
        return $this->model->last_starts;
    }

    /**
     * Gets the displayed betType price for the runner (fixed or tote)
     * @param $betType 'win' | 'place'
     * @param bool $fixed
     * @return mixed
     */
    public function getBetTypePrice($betType, $fixed = false)
    {
        $product = $this->getBetTypeProduct($betType, $fixed);

        if (!$product) {
            return null;
        }

        $price = $this->prices->filter(function ($v) use ($product) {
           return  $v->productId == $product->id;
        })->first();

        if ($price) {
            $price =  $price->{'get' . ucfirst($betType) . 'Odds'}();
            return $price >= 1 ? $price : null;
        }

        return null;
    }

    /**
     * Gets the product for the given betType
     * @param $betType String
     * @param bool $fixed
     * @return mixed
     */
    public function getBetTypeProduct($betType, $fixed = false)
    {
        if ($this->products) {
            return $this->products->filter(function ($v) use ($betType, $fixed) {
                return $v->betType == $betType && $v->fixed === $fixed;
            })->first();
        }

        return null;
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

    public function toArray()
    {
        $array = parent::toArray();

        $array['win_tote'] = $this->getBetTypePrice(BetTypeRepositoryInterface::TYPE_WIN);
        $array['place_tote'] = $this->getBetTypePrice(BetTypeRepositoryInterface::TYPE_PLACE);
        $array['win_fixed'] = $this->getBetTypePrice(BetTypeRepositoryInterface::TYPE_WIN, true);
        $array['place_fixed'] = $this->getBetTypePrice(BetTypeRepositoryInterface::TYPE_PLACE, true);

        return $array;
    }
}