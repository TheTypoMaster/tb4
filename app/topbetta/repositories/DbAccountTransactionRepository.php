<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:23
 * Project: tb4
 */

use TopBetta\Models\AccountTransactionModel;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;

class DbAccountTransactionRepository extends BaseEloquentRepository implements AccountTransactionRepositoryInterface{

    protected $model;

    public function __construct(AccountTransactionModel $model)
    {
        $this->model = $model;
    }

    public function getAccountBalanceByUserId($userId) {
        return $this->model->where('recipient_id', '=', $userId)->sum('amount');
    }

}