<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 10:01 AM
 */

namespace TopBetta\Repositories\Contracts;


interface FreeCreditTransactionRepositoryInterface {

    public function getFreeCreditBalanceForUser($userId);

    public function createTransaction($userId, $giverId, $amount, $transactionTypeId, $notes);
}