<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 3:52 PM
 */

namespace TopBetta\Services\ExternalSourceNotifications\Queue;

use Log;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class DepositExternalSourceNotificationQueueService extends AbstractTransactionExternalSourceNotificationService {

    /**
     * @var UserRepositoryInterface
     */
    private $user;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $accountTransactionRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;

    public function __construct(UserRepositoryInterface $user, AccountTransactionRepositoryInterface $accountTransactionRepository, FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository)
    {
        $this->user = $user;
        $this->accountTransactionRepository = $accountTransactionRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function formatPayload($data)
    {
        if ( ! $userId = array_get($data, 'id', null) ) {
            return array();
        }

        $payload = $this->formatUser($this->user->find($userId));

        $payload['transactions'] = $this->formatTransactions(array_get($data, 'transactions'));

        Log::info("EXTERNAL DEPOSIT PAYLOAD " . print_r($payload, true));

        return $payload;
    }

    public function getTransaction($id)
    {
        return $this->accountTransactionRepository->find($id);
    }

    public function getFreeCreditTransaction($id)
    {
        return $this->freeCreditTransactionRepository->find($id);
    }
}