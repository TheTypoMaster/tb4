<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:30 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\BetTypeModel;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class DbBetTypeRepository extends BaseEloquentRepository implements BetTypeRepositoryInterface
{

    public function __construct(BetTypeModel $model)
    {
        $this->model = $model;
    }

    public function getBetTypeByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function getBetTypes($names)
    {
        return $this->model->whereIn('name', $names)->get();
    }

}