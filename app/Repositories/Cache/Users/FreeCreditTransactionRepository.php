<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/09/2015
 * Time: 6:43 PM
 */

namespace TopBetta\Repositories\Cache\Users;


use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\DbFreeCreditTransactionRepository;

class FreeCreditTransactionRepository extends CachedResourceRepository implements FreeCreditTransactionRepositoryInterface {

    protected $storeIndividualResource = false;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(DbFreeCreditTransactionRepository $repository, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function create($data)
    {
        $transaction = $this->repository->create($data);

        if ($user = array_get($data, 'recipient_id') && $amount = array_get($data, 'amount')) {
            $this->userRepository->addFreeCreditBalance($user, $amount);
        }

        return $transaction;
    }

    public function getFreeCreditBalanceForUser($userId)
    {
        return $this->repository->getFreeCreditBalanceForUser($userId);
    }

    public function createTransaction($userId, $giverId, $amount, $transactionTypeId, $notes)
    {
        $transaction = $this->repository->createTransaction($userId, $giverId, $amount, $transactionTypeId, $notes);

        $this->userRepository->addFreeCreditBalance($userId, $amount);

        return $transaction;
    }

    public function findWithType($id)
    {
        return $this->repository->findWithType($id);
    }
}