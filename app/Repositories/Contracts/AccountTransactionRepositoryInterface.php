<?php namespace TopBetta\Repositories\Contracts;
use Carbon\Carbon;
use TopBetta\Repositories\DbAccountTransactionRepository;

/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:30
 * Project: tb4
 */


interface AccountTransactionRepositoryInterface {

    public function getAccountBalanceByUserId($userId);

    public function getTotalTransactionsForUserByTypeIn($userId, $types);

    public function getTotalTransactionsForUserByType($userId, $type);

    public function getTotalOnlyPositiveTransactionsForUserByTypeIn($userId, $types);

    public function getLastNTransactionsForUserByTypeIn($userId, $n, $types);

    public function getLastNPositiveTransactionsForUserByTypeIn($userId, $n, $types);

    public function getTotalBetTransactionsForUserByOrigin($userId, array $origin);

    public function getTotalBetWinTransactionsForUserByOrigin($userId, array $origin);

    public function getTotalBetRefundTransactionsForUserByOrigin($userId, array $origin);

    public function getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, $transactionType, array $origin);

    public function getRecentPositiveTransactionsForUserByTypeIn($userId, $dateAfter, $types);

    public function findWithType($transactionId);

    public function findForUserByTypesPaginated($user, array $types);

    public function getTransactionsForUserByDateAndType($user, Carbon $date, array $types);

    public function findLosingBetTransactionsForUser($user);
}