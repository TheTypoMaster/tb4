<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/06/2015
 * Time: 9:29 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\AdminGroupModel;
use TopBetta\Repositories\Contracts\AdminGroupsRepositoryInterface;

class DbAdminGroupsRepository extends BaseEloquentRepository implements AdminGroupsRepositoryInterface
{

    public function __construct(AdminGroupModel $model)
    {
        $this->model = $model;
    }
}