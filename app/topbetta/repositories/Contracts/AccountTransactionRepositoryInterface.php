<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:30
 * Project: tb4
 */


interface AccountTransactionRepositoryInterface {

    public function getAccountBalanceByUserId($userId);

    public function getTotalTransactionsForUserByTypeIn($userId, $types);
}