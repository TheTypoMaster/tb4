<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 3:00 PM
 */

namespace TopBetta\Services\DashboardNotification\Queue;


use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;

class UserDashboardNotificationQueueService extends AbstractTransactionDashboardNotificationService {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $transactionRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;

    public function __construct(UserRepositoryInterface $userRepository, AccountTransactionRepositoryInterface $transactionRepository, FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function getEndpoint()
    {
        return "users";
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function getTransaction($transactionId)
    {
        return $this->transactionRepository->findWithType($transactionId);
    }

    public function getFreeCreditTransaction($transactionId)
    {
        return $this->freeCreditTransactionRepository->findWithType($transactionId);
    }

    public function formatPayload($data)
    {
        //check the id exists
        if( ! $data['id'] ) {
            \Log::error("No user id specidfied in UserDashboardNotificationQueueService");
            return false;
        }

        //get the user
        $user = $this->userRepository->find($data['id']);

        $payload = $this->formatUser($user);

        //add any account transactions
        $payload['transactions'] = array();

        if( $transactions = array_get($data, 'transactions', false) ) {
            foreach($transactions as $transactionId) {
                //add the transaction
                $payload['transactions'] = $this->formatTransactions($transactionId);
            }
        }

        return $payload;
    }

}