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
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepository;

class DbFreeCreditTransactionRepository extends BaseEloquentRepository implements FreeCreditTransactionRepository {

    public function __construct(FreeCreditTransactionModel $freeCreditTransaction)
    {
        $this->model = $freeCreditTransaction;
    }

    public function getFreeCreditBalanceForUser($userId)
    {
        return $this    -> model
                            -> where('recipient_id', '=', $userId)
                            -> sum('amount');


    }

    public function createTransaction($userId, $amount, $transactionTypeId, $notes)
    {
        return $this->create(array(
            "recipient_id"                      => $userId,
            "giver_id"                          => '6996',
            "session_id"                        => '-1',
            "amount"                            => $amount,
            "tournament_transaction_type_id"    => $transactionTypeId,
            "notes"                             => $notes,
            "created_date"                       => Carbon::now()->format("Y-m-d H:i:s"),
        ));
    }

}