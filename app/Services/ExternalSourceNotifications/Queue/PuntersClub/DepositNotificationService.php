<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/06/2015
 * Time: 11:42 AM
 */

namespace TopBetta\Services\ExternalSourceNotifications\Queue\PuntersClub;

use Log;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\ExternalSourceNotifications\Queue\ExternalSourceNotificationQueueService;

class DepositNotificationService extends ExternalSourceNotificationQueueService {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $accountTransactionRepository;

    public function __construct(UserRepositoryInterface $userRepository,
                                AccountTransactionRepositoryInterface $accountTransactionRepository,
                                BetSourceRepositoryInterface $betSourceRepository)
    {
        $this->userRepository = $userRepository;
        $this->accountTransactionRepository = $accountTransactionRepository;
        parent::__construct($betSourceRepository);
    }

    public function formatPayload($data)
    {
        if ( ! $userId = array_get($data, 'id', null) ) {
            return array();
        }

        //get the user
        $user = $this->userRepository->find($userId);

        //get the child user
        if( ! $childUser = array_get($data, 'child_id', null) ) {
            return array();
        }

        $childUser = $this->userRepository->find($childUser);

        //get the transaction
        $transaction = array_get($data, 'transaction', null);
        $transaction = $this->accountTransactionRepository->find($transaction);

        //create the payload
        $payload = array(
            "child_username" => $childUser->username,
            "parent_username" => $user->username,
            "transaction_amount" => $transaction->amount,
            "transaction_type" => $transaction->transactionType->keyword,
            "transaction_date" => $transaction->created_date,
        );

        Log::info('DEPOSIT PUNTERSCLUB PAYLOAD ' . print_r($payload, true));

        return $payload;
    }
}