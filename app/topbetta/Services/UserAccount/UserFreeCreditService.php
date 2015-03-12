<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 1:20 PM
 */

namespace TopBetta\Services\UserAccount;


use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use Log;

class UserFreeCreditService {

    const   REMOVE_CHUNK_SIZE           = 100,
            REMOVE_GIVER_ID             = 6996,
            REMOVE_TRANSACTION_TYPE     = 7;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var FreeCreditTransactionRepository
     */
    private $freeCreditTransactionRepository;


    public function __construct(UserRepositoryInterface $userRepository,
                                FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function getFreeCreditBalanceForUser($userId)
    {
        return $this->freeCreditTransactionRepository->getFreeCreditBalanceForUser($userId);
    }

    public function removeCreditsFromInactiveUsers($start, $end)
    {
        $users = $this->userRepository->getUsersWithLastActivityBetween($start, $end, $page = 0, self::REMOVE_CHUNK_SIZE);

        //do some chunking so we don't use too much memory
        while(count($users) > 0) {

            foreach($users as $user) {
                //check the users free credit balance
                $balance = $this->freeCreditTransactionRepository->getFreeCreditBalanceForUser($user->id);

                if($balance > 0){
                    //the user has free credits so remove them
                    $this->decreaseFreeCreditBalance(
                        $user->id,
                        self::REMOVE_GIVER_ID,
                        $balance,
                        self::REMOVE_TRANSACTION_TYPE,
                        "Removal of free credit due to account inactivity"
                    );
                }

            }

            //get the next chunk of users
            $page++;
            $users = $this->userRepository->getUsersWithLastActivityBetween($start, $end, $page, self::REMOVE_CHUNK_SIZE);
        }

    }

    public function increaseFreeCreditBalance($userId, $giverId, $amount, $transactionType, $notes)
    {
        return $this->freeCreditTransactionRepository->createTransaction($userId, $giverId, $amount, $transactionType, $notes);
    }

    public function decreaseFreeCreditBalance($userId, $giverId, $amount, $transactionType, $notes)
    {
        return $this->increaseFreeCreditBalance($userId, $giverId, -$amount, $transactionType, $notes);
    }
}