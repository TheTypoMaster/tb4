<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/09/2015
 * Time: 4:30 PM
 */

namespace TopBetta\Repositories\Cache;


use Carbon\Carbon;
use TopBetta\Models\SelectionPricesModel;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;

class RacingSelectionPriceRepository extends CachedResourceRepository {

    protected static $modelClass = 'TopBetta\Models\SelectionPricesModel';

    protected $cachePrefix = 'racing_selection_prices_';


    protected $tags = array("racing", "selection_prices");

    public function __construct(SelectionPriceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPriceForSelectionByProduct($id, $product)
    {
        $model = \Cache::tags($this->tags)->get($this->cachePrefix . $id . '_' . $product);

        if (!$model) {
            return $this->repository->getPriceForSelectionByProduct($id, $product);
        }

        $model = new SelectionPricesModel($model);
        $model->syncOriginal();
        $model->exists = true;

        return $model;
    }

    public function makeCacheResource($model)
    {
        \Cache::tags($this->tags)->put($this->cachePrefix . $model->selection_id . '_' . $model->bet_product_id, $model->toArray(), Carbon::now()->addDays(1)->diffInMinutes());

        return $model;
    }

}