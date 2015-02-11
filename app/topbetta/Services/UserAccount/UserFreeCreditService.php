<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 1:20 PM
 */

namespace TopBetta\Services\UserAccount;


use TopBetta\Repositories\Contracts\FreeCreditTransactionRepository;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use Log;

class UserFreeCreditService {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var FreeCreditTransactionRepository
     */
    private $freeCreditTransactionRepository;


    public function __construct(UserRepositoryInterface $userRepository,
                                FreeCreditTransactionRepository $freeCreditTransactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function removeCreditsFromInactiveUsers($start, $end)
    {
        $users = $this->userRepository->getUsersWithLastActivityBetween($start, $end, $page = 0, $count = 50);

        Log::debug("USER COUNT: ".count($users));
        //do some chunking so we don't use too much memory
        while(count($users) > 0) {

            foreach($users as $user) {
                //check the users free credit balance
                $balance = $this->freeCreditTransactionRepository->getFreeCreditBalanceForUser($user->id);

                if($balance > 0){
                    //the user has free credits so remove them
                    $this->decreaseFreeCreditBalance($user->id, $balance, 7, "Removed");
                }

            }

            Log::debug($page);
            //get the next chunk of users
            $page++;
            $users = $this->userRepository->getUsersWithLastActivityBetween($start, $end, $page, $count);
        }


    }

    public function increaseFreeCreditBalance($userId, $amount, $transactionType, $notes)
    {
        return $this->freeCreditTransactionRepository->createTransaction($userId, $amount, $transactionType, $notes);
    }

    public function decreaseFreeCreditBalance($userId, $amount, $transactionType, $notes)
    {
        return $this->increaseFreeCreditBalance($userId, -$amount, $transactionType, $notes);
    }
}