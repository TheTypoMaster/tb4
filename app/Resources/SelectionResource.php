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
        'id'         => 'id',
        'name'       => 'name',
        'number'     => 'number',
        'jockey'     => 'associate',
        'barrier'    => 'barrier',
        'handicap'   => 'handicap',
        'weight'     => 'weight',
        'prices'     => 'prices',
        'silk'       => 'silk',
        'form'       => 'form',
        'typeCode'   => 'type_code',
    );

    protected $types = array(
        "id" => "int",
        "number" => "int",
        "winOdds" => "float",
        "placeOdds" => "float",
        "silk_id" => "int",
        "barrier" => "int",
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
                "product_id" => $this->products->get(BetTypeRepositoryInterface::TYPE_WIN)->id,
                "product" => $this->products->get(BetTypeRepositoryInterface::TYPE_WIN)->productCode,
                "bet_type" => BetTypeRepositoryInterface::TYPE_WIN,
                "price" => $this->getWinOdds(),
            ),
            array(
                "product_id" => $this->products->get(BetTypeRepositoryInterface::TYPE_PLACE)->id,
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

    public function getForm()
    {
        return $this->model->last_starts;
    }

    public function getSilk()
    {
        if ($this->model->type_code == 'G') {
            return Config::get('silks.greyhound_silk_path') . Config::get('silks.greyhound_silk_filename_prefix') .
                $this->model->number . Config::get('silks.default_silk_file_extension');
        }

        return Config::get('silks.default_silk_path') . $this->model->silk_id . Config::get('silks.default_silk_file_extension');
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

        $array['win_tote'] = $this->getWinOdds();
        $array['place_tote'] = $this->getPlaceOdds();
        $array['win_fixed'] = null;
        $array['place_fixed'] = null;

        return $array;
    }
}