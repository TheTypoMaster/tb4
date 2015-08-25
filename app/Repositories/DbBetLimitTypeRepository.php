<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/06/2015
 * Time: 3:58 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\BetLimitTypeModel;
use TopBetta\Repositories\Contracts\BetLimitTypeRepositoryInterface;

class DbBetLimitTypeRepository extends BaseEloquentRepository implements BetLimitTypeRepositoryInterface
{

    public function __construct(BetLimitTypeModel $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->where('name', $name)->get();
    }
}