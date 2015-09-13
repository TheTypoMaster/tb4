<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 10:31 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\AffiliateTypesModel;
use TopBetta\Repositories\Contracts\AffiliateTypeRepositoryInterface;

class DbAffiliateTypeRepository extends BaseEloquentRepository implements AffiliateTypeRepositoryInterface
{

    public function __construct(AffiliateTypesModel $model)
    {
        $this->model = $model;
    }
}