<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:51
 * Project: tb4
 */

use TopBetta\Models\AccountTransactionTypeModel;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;

class DbAccountTransactionTypeRepository extends BaseEloquentRepository implements AccountTransactionTypeRepositoryInterface{

    protected $model;

    public function __construct(AccountTransactionTypeModel $model)
    {
        $this->model = $model;
    }

    public function getTransactionTypeByKeyword($keyword)
    {
        $transactionType = $this->model->where('keyword', '=', $keyword)
                                         ->first();
        if ($transactionType) return $transactionType->toArray();

        return false;
    }


}