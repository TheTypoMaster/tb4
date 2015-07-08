<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/02/2015
 * Time: 4:09 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\SelectionStatusModel;
use TopBetta\Repositories\Contracts\SelectionStatusRepositoryInterface;

class DbSelectionStatusRepository extends BaseEloquentRepository implements SelectionStatusRepositoryInterface
{

    public function __construct(SelectionStatusModel $model) {
        $this->model = $model;
    }

    public function getSelectionStatusIdByName($statusName)
    {
        return $this->model->where("name", "=", $statusName)->value("id");
    }

}