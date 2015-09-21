<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/09/2015
 * Time: 5:37 PM
 */

namespace TopBetta\Repositories\Cache\Users;


use Carbon\Carbon;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\DbAccountTransactionRepository;

class AccountTransactionRepository extends CachedResourceRepository implements AccountTransactionRepositoryInterface
{
    protected $storeIndividualResource = false;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(DbAccountTransactionRepository $repository, UserRepository $userRepository)
    {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
    }

    public function create($data)
    {
        $transaction = $this->repository->create($data);

        if (($user = array_get($data, 'recipient_id')) && ($amount = array_get($data, 'amount'))) {
            $this->userRepository->addAccountBalance($user, $amount);
        }

        return $transaction;
    }

    public function getAccountBalanceByUserId($userId)
    {
        return $this->repository->getAccountBalanceByUserId($userId);
    }

    public function getTotalTransactionsForUserByTypeIn($userId, $types)
    {
        return $this->repository->getTotalTransactionsForUserByTypeIn($userId, $types);
    }

    public function getTotalTransactionsForUserByType($userId, $type)
    {
        return $this->repository->getTotalTransactionsForUserByType($userId, $type);
    }

    public function getTotalOnlyPositiveTransactionsForUserByTypeIn($userId, $types)
    {
        return $this->repository->getTotalOnlyPositiveTransactionsForUserByTypeIn($userId, $types);
    }

    public function getLastNTransactionsForUserByTypeIn($userId, $n, $types)
    {
        return $this->repository->getLastNTransactionsForUserByTypeIn($userId, $n, $types);
    }

    public function getLastNPositiveTransactionsForUserByTypeIn($userId, $n, $types)
    {
        return $this->repository->getLastNPositiveTransactionsForUserByTypeIn($userId, $n, $types);
    }

    public function getTotalBetTransactionsForUserByOrigin($userId, array $origin)
    {
        return $this->repository->getTotalBetTransactionsForUserByOrigin($userId, $origin);
    }

    public function getTotalBetWinTransactionsForUserByOrigin($userId, array $origin)
    {
        return $this->repository->getTotalBetWinTransactionsForUserByOrigin($userId, $origin);
    }

    public function getTotalBetRefundTransactionsForUserByOrigin($userId, array $origin)
    {
        return $this->repository->getTotalBetRefundTransactionsForUserByOrigin($userId, $origin);
    }

    public function getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, $transactionType, array $origin)
    {
        return $this->repository->getTotalBetTransactionsForUserByTransactionTypeAndOrigin($userId, $transactionType, $origin);
    }

    public function getRecentPositiveTransactionsForUserByTypeIn($userId, $dateAfter, $types)
    {
        return $this->repository->getRecentPositiveTransactionsForUserByTypeIn($userId, $dateAfter, $types);
    }

    public function findWithType($transactionId)
    {
        return $this->repository->findWithType($transactionId);
    }

    public function findForUserByTypesPaginated($user, array $types)
    {
        return $this->repository->findForUserByTypesPaginated($user, $types);
    }

    public function getTransactionsForUserByDateAndType($user, Carbon $date, array $types)
    {
        return $this->repository->getTransactionsForUserByDateAndType($user, $date, $types);
    }

    public function findLosingBetTransactionsForUser($user)
    {
        return $this->repository->findLosingBetTransactionsForUser($user);
    }

}