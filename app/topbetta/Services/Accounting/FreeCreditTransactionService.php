<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 9:59 AM
 */

namespace TopBetta\Services\Accounting;


use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface;

class FreeCreditTransactionService {

    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;
    /**
     * @var FreeCreditTransactionTypeRepositoryInterface
     */
    private $freeCreditTransactionTypeRepository;

    public function __construct(FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository, FreeCreditTransactionTypeRepositoryInterface $freeCreditTransactionTypeRepository)
    {
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
        $this->freeCreditTransactionTypeRepository = $freeCreditTransactionTypeRepository;
    }

    public function increaseFreeCreditBalance($userId, $giverId, $amount, $transactionType, $notes = null)
    {
        $transactionType = $this->freeCreditTransactionTypeRepository->getByName($transactionType);

        if( ! $notes ) {
            $notes = $transactionType->description;
        }

        return $this->freeCreditTransactionRepository->createTransaction($userId, $giverId, $amount, $transactionType->id, $notes);
    }

    public function decreaseFreeCreditBalance($userId, $giverId, $amount, $transactionType, $notes = null)
    {
        return $this->increaseFreeCreditBalance($userId, $giverId, -$amount, $transactionType, $notes);
    }
}