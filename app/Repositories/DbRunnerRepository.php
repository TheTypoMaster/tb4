<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 2:45 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\RunnerModel;
use TopBetta\Repositories\Contracts\RunnerRepositoryInterface;

class DbRunnerRepository extends BaseEloquentRepository implements RunnerRepositoryInterface
{

    public function __construct(RunnerModel $model)
    {
        $this->model = $model;
    }
}