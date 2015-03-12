<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/02/2015
 * Time: 10:57 AM
 */

namespace TopBetta\Services\Accounting;

use Config;
use Auth;
use TopBetta\Repositories\Contracts\PoliTransactionRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\Exceptions\TransactionNotFoundException;
use TopBetta\Models\PoliTransactionModel;

class PoliTransactionService
{

    const ACCT_TRANSACTION_KEYWORD = "polideposit";
    /**
     * @var PoliTransactionRepositoryInterface
     */
    private $poliTransactionRepository;

    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;

    public function __construct(PoliTransactionRepositoryInterface $poliTransactionRepository, AccountTransactionService $accountTransactionService)
    {
        $this->poliTransactionRepository = $poliTransactionRepository;
        $this->accountTransactionService = $accountTransactionService;
    }


    public function createTransaction($userId, $amount, $currencyCode)
    {
        //create the transaction in the db
        return $this->poliTransactionRepository->create(array(
            "user_id"       => $userId,
            "status"        => "Not Initialized",
            "amount"        => $amount,
            "currency_code" => $currencyCode,
        ));

    }

    public function updateTransactionTokenAndStatus($transactionRefNo, $token, $transactionStatus, $errorCode = 0)
    {
        $poliTransaction = $this->poliTransactionRepository->findByRefNo($transactionRefNo);

        if(! $poliTransaction ) {
            throw new TransactionNotFoundException;
        }

        //sets the token if necessary
        $poliTransaction = $this->poliTransactionRepository->setToken($poliTransaction, $token);

        //Only change the status if the transaction is not in a terminal state
        //This stops account balance being incremented more than once.
        if( ! self::statusIsTerminal($poliTransaction->status) ) {
            $poliTransaction = $this->updateStatus($poliTransaction, $transactionStatus, $errorCode);
        }

        return $poliTransaction;
    }

    public function updateStatus($poliTransaction, $transactionStatus, $errorCode = 0)
    {

        $poliTransaction = $this->poliTransactionRepository->updateStatus($poliTransaction, $transactionStatus, $errorCode);

        //Increase the account balance if completed
        if( $poliTransaction->isCompleted() ) {
            $this->accountTransactionService->increaseAccountBalance(
                $poliTransaction->user_id,
                $poliTransaction->amount,
                self::ACCT_TRANSACTION_KEYWORD,
                "Poli Transaction id: ".$poliTransaction->poli_ref_no
            );
        }

        return $poliTransaction;
    }


    public function initialize($poliTransactionId, $refNo)
    {
        return $this->poliTransactionRepository->initialize($poliTransactionId, $refNo);
    }

    public function initializationFailed($poliTransactionId, $errorCode)
    {
        return $this->poliTransactionRepository->initializationFailed($poliTransactionId, $errorCode);
    }

    /**
     * Checks if the given transaction status is a terminal status
     * @param $transactionStatus
     * @return bool
     */
    public static function statusIsTerminal($transactionStatus)
    {
        switch($transactionStatus){
            case PoliTransactionModel::STATUS_RECEIPT_UNVERIFIED:
            case PoliTransactionModel::STATUS_COMPLETED:
            case PoliTransactionModel::STATUS_INCOMPATIBLE:
            case PoliTransactionModel::STATUS_REJECTED:
            case PoliTransactionModel::STATUS_FAILED:
            case PoliTransactionModel::STATUS_CANCELLED:
            case PoliTransactionModel::STATUS_TIMED_OUT:
                return true;
        }

        return false;
    }


}

