<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 9:45 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\AffiliatesModel;
use TopBetta\Repositories\Contracts\AffiliateRepositoryInterface;

class DbAffiliateRepository extends BaseEloquentRepository implements AffiliateRepositoryInterface
{

    public function __construct(AffiliatesModel $model)
    {
        $this->model = $model;
    }

    public function getByCodeOrFail($code)
    {
        return $this->model->where('affiliate_code', $code)->firstOrFail();
    }
}