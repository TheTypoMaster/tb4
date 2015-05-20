<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 10:51 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\TODModel;
use TopBetta\Repositories\Contracts\TODRepositoryInterface;

class DbTODRepository extends BaseEloquentRepository implements TODRepositoryInterface
{

    public function __construct(TODModel $model)
    {
        $this->model = $model;
    }
}