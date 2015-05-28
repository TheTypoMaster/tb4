<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 4:46 PM
 */

namespace TopBetta\Repositories;


use TopBetta\BetResultStatus;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;

class DbBetResultStatusRepository extends BaseEloquentRepository implements BetResultStatusRepositoryInterface
{

    public function __construct(BetResultStatus $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}