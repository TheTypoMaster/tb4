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

    const BET_TRANSACTION_BET    = 'tbdb_bet.bet_transaction_id';
    const BET_TRANSACTION_REFUND = 'tbdb_bet.refund_transaction_id';
    const BET_TRANSACTION_WIN    = 'tbdb_bet.result_transaction_id';

    protected $model;

    protected $order = array('created_date', 'DESC');

    public function __construct(AccountTransactionModel $model)
    {
        $this->model = $model;
    }

    public function findAllWithTypePaged($page, $count, $startDate = null, $endDate = null)
    {
        $model = $this->model->orderBy('created_date', 'DESC')->with('transactionType');

        if($startDate) {
            $model->where('created_date', '>=', $startDate);
        }

        if($endDate) {
            $model->where('created_date', '<=', $endDate);
        }

        return $model->forPage($page, $count)->get();
    }

    public function findWithType($transactionId)
    {
        return $this->model->with(array('transactionType', 'giver', 'giver.topbettauser'))->where('id', $transactionId)->first()->toArray();
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
	
    public function getUserTransactionsPaginated($userId) {
        return $this->model
            ->where('recipient_id', $userId)
            ->with('transactionType', 'giver', 'recipient')
            ->orderBy('created_date', 'DESC')
            ->paginate();
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
    public function getTotalOnlyPositiveTransactionsForUserByTypeIn($userId, $types, $startDate = null, $endDate = null)
    {
        $model = $this
            ->model
            ->where('recipient_id', '=', $userId)
            ->where('amount', '>', 0)
            ->whereIn('account_transaction_type_id', $types);

        if( $startDate ) {
            $model->where('created_date', '>=', $startDate);
        }

        if( $endDate ) {
            $model->where('created_date', '<=', $endDate);
        }

        return $model->sum('amount');
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
            ->join('tbdb_bet', function($join) use ($transactionType, $userId) {
                $join->on('tbdb_account_transaction.id', '=', $transactionType);
                $join->on('tbdb_bet.user_id', '=', \DB::raw($userId));
            })
            ->where('recipient_id', '=', $userId)
            ->whereIn('bet_origin_id', $origin)
            ->sum('tbdb_account_transaction.amount');
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

    public function findAllPaginatedForUser($user)
    {
        return $this->model
            ->join('tbdb_account_transaction_type as att', 'att.id', '=', 'tbdb_account_transaction.account_transaction_type_id')
            ->where('recipient_id', $user)
            ->with('transactionType')
            ->orderBy($this->order[0], $this->order[1])
            ->select(array('tbdb_account_transaction.*', 'att.name as name', 'att.description as description'))
            ->paginate();
    }

    public function findForUserByTypesPaginated($user, array $types)
    {
        return $this->model
            ->join('tbdb_account_transaction_type as att', 'att.id', '=', 'tbdb_account_transaction.account_transaction_type_id')
            ->where('recipient_id', $user)
            ->whereIn('att.keyword', $types)
            ->with('transactionType')
            ->orderBy($this->order[0], $this->order[1])
            ->select(array('tbdb_account_transaction.*', 'att.name as name', 'att.description as description'))
            ->paginate();
    }

}