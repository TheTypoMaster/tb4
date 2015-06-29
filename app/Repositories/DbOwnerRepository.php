<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 2:45 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\OwnerModel;
use TopBetta\Repositories\Contracts\OwnerRepositoryInterface;

class DbOwnerRepository extends BaseEloquentRepository implements OwnerRepositoryInterface
{

    public function __construct(OwnerModel $model)
    {
        $this->model = $model;
    }

    public function getByExternalId($externalId)
    {
        return $this->model->where('external_owner_id', $externalId)->first();
    }
}