<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/08/2015
 * Time: 2:50 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\ResultPriceModel;
use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;

class DbResultPricesRepository extends BaseEloquentRepository implements ResultPricesRepositoryInterface
{

    public function __construct(ResultPriceModel $model)
    {
        $this->model = $model;
    }
}