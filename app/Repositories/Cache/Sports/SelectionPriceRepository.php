<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/09/2015
 * Time: 4:56 PM
 */

namespace TopBetta\Repositories\Cache\Sports;


use Carbon\Carbon;
use TopBetta\Models\SelectionPricesModel;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;

class SelectionPriceRepository extends CachedResourceRepository {

    protected $tags = array("sports", "selections");

    protected $storeIndividual = false;

    protected $resourceClass = 'TopBetta\Resources\Sports\SelectionResource';

    protected $cachePrefix = 'selection_prices';

    public function __construct(SelectionPriceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPriceForSelection($id)
    {
        $model = \Cache::tags($this->tags)->get($this->cachePrefix . $id);

        if (!$model) {
            return $this->repository->getPriceForSelection($id);
        }

        $model = new SelectionPricesModel($model);
        $model->syncOriginal();
        $model->exists = true;

        return $model;
    }

    public function makeCacheResource($model)
    {
        \Cache::tags($this->tags)->put($this->cachePrefix . $model->selection_id, $model->toArray(), Carbon::now()->addDays(2)->diffInMinutes());

        return $model;
    }
}