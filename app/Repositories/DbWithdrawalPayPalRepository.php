<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 12:34 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\WithdrawalPaypal;
use TopBetta\Repositories\Contracts\WithdrawalPayPalRepositoryInterface;

class DbWithdrawalPayPalRepository extends BaseEloquentRepository implements WithdrawalPayPalRepositoryInterface
{

    public function __construct(WithdrawalPaypal $model)
    {
        $this->model = $model;
    }
}