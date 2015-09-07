<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:40 AM
 */

namespace TopBetta\Resources;

use Config;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class SelectionResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id'             => 'id',
        'name'           => 'name',
        'number'         => 'number',
        'jockey'         => 'associate',
        'barrier'        => 'barrier',
        'handicap'       => 'handicap',
        'weight'         => 'weight',
        'silk'           => 'silk',
        'form'           => 'form',
        'winDeductions'  => 'win_deductions',
        'placeDeduction' => 'place_deductions',
        'typeCode'       => 'typeCode',
        'selectionStatus' => 'selectionstatus.keyword',
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
        'prices',
        'runner',
        'runner.owner',
        'runner.trainer',
        'form',
        'lastStarts',
        'selectionstatus',
    );

    private $products = null;

    private $typeCode = null;

    public function __construct($model)
    {
        $model->load($this->loadRelations);

        parent::__construct($model);

    }

    public function runner()
    {
        return $this->item('runner', 'TopBetta\Resources\RunnerResource', 'runner');
    }

    /**
     * Hacky way to set prices in products
     * @return mixed
     */
    public function prices()
    {
        return $this->collection('prices', 'TopBetta\Resources\PriceResource', 'prices');
    }

    public function addPrice($price)
    {
        $prices = $this->prices()->keyBy('id');

        $prices->put($price->id, new PriceResource($price));

        $this->relations['prices'] = $prices->values();
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

    public function getSilk()
    {
        if ($this->getTypeCode() == 'G') {
            return Config::get('silks.greyhound_silk_path') . Config::get('silks.greyhound_silk_filename_prefix') .
            $this->model->number . Config::get('silks.default_silk_file_extension');
        }

        $silk = $this->model->silk_id;

        if(!$silk) {
            $silk = Config::get('silks.default_silk_filename');
        }

        return Config::get('silks.default_silk_path') . $silk . Config::get('silks.default_silk_file_extension');
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

    /**
     * @return null
     */
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * @param null $typeCode
     * @return $this
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;
        return $this;
    }

    protected function initialize()
    {
        parent::initialize();

        $tempModel = clone $this->model;
        $this->setTypeCode($tempModel->market->event->competition->first()->type_code);
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