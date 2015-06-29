<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 9:26 AM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\IconTypeModel;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;

class DbIconTypeRepository extends BaseEloquentRepository implements IconTypeRepositoryInterface
{

    public function __construct(IconTypeModel $iconTypeModel)
    {
        $this->model = $iconTypeModel;
    }

    public function getIconTypeByName($name)
    {
        return $this->model->where('name', '=', $name)->first();
    }
}