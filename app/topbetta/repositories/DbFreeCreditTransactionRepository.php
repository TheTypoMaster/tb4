<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 10:06 AM
 */

namespace TopBetta\Repositories;

use DB;
use Carbon\Carbon;
use TopBetta\models\FreeCreditTransactionModel;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;

class DbFreeCreditTransactionRepository extends BaseEloquentRepository implements FreeCreditTransactionRepositoryInterface {

    public function __construct(FreeCreditTransactionModel $freeCreditTransaction)
    {
        $this->model = $freeCreditTransaction;
    }

    public function findWithType($id)
    {
        return $this->model->with('transactionType')->where('id', $id)->first()->toArray();
    }


    public function getFreeCreditBalanceForUser($userId)
    {
        return $this    -> model
                        -> where('recipient_id', '=', $userId)
                        -> where('amount', '!=', '0')
                        -> sum('amount');
    }

    public function createTransaction($userId, $giverId, $amount, $transactionTypeId, $notes)
    {
        return $this->create(array(
            "recipient_id"                      => $userId,
            "giver_id"                          => $giverId,
            "session_tracking_id"               => -1,
            "amount"                            => $amount,
            "tournament_transaction_type_id"    => $transactionTypeId,
            "notes"                             => $notes,
            "created_date"                      => Carbon::now()->toDateTimeString(),
        ));
    }

}