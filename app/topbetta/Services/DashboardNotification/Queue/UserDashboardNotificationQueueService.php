<?php namespace TopBetta\Services\DashboardNotification\Queue;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 3:00 PM
 */


use Log;

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
		if(!array_get($data, 'id', false)){
			Log::error("UserDashboardNotificationQueueService: No user id specified in payload - ", $data);
			return array();
		}

        //get the user
        $user = $this->userRepository->getWithTopBettaUser($data['id'])->toArray();

        $payload = $this->formatUser($user);

        //add any account transactions
        $payload['transactions'] = array();

        if( $transactions = array_get($data, 'transactions', false) ) {
            //add the transactions
            $payload['transactions'] = $this->formatTransactions($transactions);
        }

        if( $transactions = array_get($data, 'free-credit-transactions', false) ) {
            //add the free transaction
            $payload['transactions'] = array_merge($payload['transactions'], $this->formatTransactions($transactions, true));
        }

        return $payload;
    }

}