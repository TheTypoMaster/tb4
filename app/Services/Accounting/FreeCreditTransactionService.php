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
     * @var FreeCreditTransactionTypeRepositoryInterface
     */
    private $freeCreditTransactionTypeRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;

    public function __construct(FreeCreditTransactionTypeRepositoryInterface $freeCreditTransactionTypeRepository, FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository)
    {
        $this->freeCreditTransactionTypeRepository = $freeCreditTransactionTypeRepository;
        $this->freeCreditTransactionRepository     = $freeCreditTransactionRepository;
    }

    public function increaseBalance($userId, $amount, $transactionType, $giverId = -1, $notes = '')
    {
        $type = $this->freeCreditTransactionTypeRepository->getByName($transactionType);

        if (! $notes) {
            $notes = $type->description;
        }

        return $this->freeCreditTransactionRepository->createTransaction($userId, $giverId, $amount, $type->id, $notes);
    }
}