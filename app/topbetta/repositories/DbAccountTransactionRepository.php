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

    const BET_TRANSACTION_BET    = 'bet';
    const BET_TRANSACTION_REFUND = 'betRefund';
    const BET_TRANSACTION_WIN    = 'betWin';

    protected $model;

    public function __construct(AccountTransactionModel $model)
    {
        $this->model = $model;
    }

    public function findWithType($transactionId)
    {
        return $this->model->with(array('transactionType', 'giver'))->where('id', $transactionId)->first()->toArray();
    }

    public function getAccountBalanceByUserId($userId) {
        return $this->model->where('recipient_id', '=', $userId)->sum('amount');
    }

    public function getTransactionWithUsers($transactionId)
    {
        return $this
            ->model
            ->where('id', $transactionId)
            ->with(array('transactionType', 'recipient', 'giver'))
            ->first()->toArray();
    }

    public function getTotalTransactionsForUserByType($userId, $type)
    {
        return $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->where('account_transaction_type_id', '=', $type)
            ->sum('amount');
    }

    public function getTotalTransactionsForUserByTypeIn($userId, $types)
    {
        return $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->whereIn('account_transaction_type_id', $types)
            ->sum('amount');
    }

    /**
     * Used as in the database deposit transaction type are sometimes used for withdrawals.
     * @param $userId
     * @param $types
     * @return mixed
     */
    public function getTotalOnlyPositiveTransactionsForUserByTypeIn($userId, $types)
    {
        return $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->where('amount', '>', 0)
            ->whereIn('account_transaction_type_id', $types)
            ->sum('amount');
    }


    public function getLastNTransactionsForUserByTypeIn($userId, $n, $types)
    {
        return $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->whereIn('account_transaction_type_id', $types)
            ->orderBy('created_date', 'DESC')
            ->take($n)
            ->get();
    }

    public function getLastNPositiveTransactionsForUserByTypeIn($userId, $n, $types)
    {
        return $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->where('amount', '>', 0)
            ->whereIn('account_transaction_type_id', $types)
            ->orderBy('created_date', 'DESC')
            ->take($n)
            ->get();
    }


    // ----- TRANSACTION ASSOSCIATED WITH BETS ----

    public function getTotalBetTransactionsForUserByOrigin($userId, array $origin)
    {
        return $this->getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, self::BET_TRANSACTION_BET, $origin);
    }

    public function getTotalBetWinTransactionsForUserByOrigin($userId, array $origin)
    {
        return $this->getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, self::BET_TRANSACTION_WIN, $origin);
    }

    public function getTotalBetRefundTransactionsForUserByOrigin($userId, array $origin)
    {
        return $this->getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, self::BET_TRANSACTION_REFUND, $origin);
    }

    public function getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, $transactionType, array $origin)
    {
        return $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->whereHas($transactionType, function($q) use ($origin) {
                $q->whereIn('bet_origin_id', $origin);
            })
            ->sum('amount');
    }

    public function getRecentPositiveTransactionsForUserByTypeIn($userId, $dateAfter, $types)
    {
        return $this
            ->model
            ->where('recipient_id', $userId)
            ->where('amount', '>', 0)
            ->whereIn('account_transaction_type_id', $types)
            ->where('created_date', '>=', $dateAfter)
            ->orderBy('created_date', 'DESC')
            ->get();
    }

}