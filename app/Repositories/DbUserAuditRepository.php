<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 2:41 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\UserAudit;
use TopBetta\Repositories\Contracts\UserAuditRepositoryInterface;

class DbUserAuditRepository extends BaseEloquentRepository implements UserAuditRepositoryInterface
{

    public function __construct(UserAudit $model)
    {
        $this->model = $model;
    }
}