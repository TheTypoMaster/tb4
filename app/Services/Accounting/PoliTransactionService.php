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
use TopBetta\Services\Notifications\EmailNotificationService;

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
    /**
     * @var EmailNotificationService
     */
    private $notificationService;

    public function __construct(PoliTransactionRepositoryInterface $poliTransactionRepository, AccountTransactionService $accountTransactionService, EmailNotificationService $notificationService)
    {
        $this->poliTransactionRepository = $poliTransactionRepository;
        $this->accountTransactionService = $accountTransactionService;
        $this->notificationService = $notificationService;
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

    public function updateTransactionTokenStatusAndAmount($transactionRefNo, $token, $transactionStatus, $amount, $errorCode = 0)
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
            $poliTransaction = $this->updateStatusAndAmount($poliTransaction, $transactionStatus, $amount, $errorCode);
        }

        return $poliTransaction;
    }

    public function updateStatusAndAmount($poliTransaction, $transactionStatus, $amount, $errorCode = 0)
    {

        $poliTransaction = $this->poliTransactionRepository->updateStatusAndAmount($poliTransaction, $transactionStatus, $amount, $errorCode);

        //Increase the account balance if completed
        if( $poliTransaction->isCompleted() ) {
            $this->accountTransactionService->increaseAccountBalance(
                $poliTransaction->user_id,
                $poliTransaction->amount,
                self::ACCT_TRANSACTION_KEYWORD,
                "Poli Transaction id: ".$poliTransaction->poli_ref_no
            );
        } else if ($transactionStatus == PoliTransactionModel::STATUS_RECEIPT_UNVERIFIED) {
            \Log::error("POLI DEPOSIT RECEIPT UNVERIFIED " . $poliTransaction->poli_token);
            $this->notificationService->notifyByEmail(
                Config::get('mail.from.address'),
                Config::get('mail.from.name'),
                Config::get('mail.from.address'),
                Config::get('mail.from.name'),
                "POLI DEPOSIT RECEIPT UNVERIFIED " . $poliTransaction->poli_token,
                "A POLI transaction was created with transaction status " . PoliTransactionModel::STATUS_RECEIPT_UNVERIFIED . " transaction token " . $poliTransaction->poli_token
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

    public static function simplifyTransactionStatus($transactionStatus)
    {
        switch($transactionStatus) {
            case PoliTransactionModel::STATUS_INITIATED:
            case PoliTransactionModel::STATUS_FINANCIAL_INSTITUTION_SELECETED:
            case PoliTransactionModel::STATUS_EULA_ACCEPTED:
            case PoliTransactionModel::STATUS_IN_PROCESS:
            case PoliTransactionModel::STATUS_UNKNOWN:
                return 'pending';
            case PoliTransactionModel::STATUS_RECEIPT_UNVERIFIED:
            case PoliTransactionModel::STATUS_INCOMPATIBLE:
            case PoliTransactionModel::STATUS_REJECTED:
            case PoliTransactionModel::STATUS_CANCELLED:
            case PoliTransactionModel::STATUS_TIMED_OUT:
                return 'failed';
            case PoliTransactionModel::STATUS_COMPLETED:
                return 'success';
        }

        throw new \InvalidArgumentException("Invalid transaction status " . $transactionStatus);


    }


}

