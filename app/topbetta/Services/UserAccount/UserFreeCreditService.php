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

class UserFreeCreditService {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var FreeCreditTransactionRepository
     */
    private $freeCreditTransactionRepository;

    public function __construct(UserRepositoryInterface $userRepository, FreeCreditTransactionRepository $freeCreditTransactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function removeCreditsFromInactiveUsers($days)
    {
        $users = $this->userRepository->getInactiveForNDaysUser($days, $page = 0, $count = 100);

        while($users) {
            var_dump($page);

            $page++;
            $users = $this->userRepository->getInactiveForNDaysUser($days, $page, $count);
        }
    }
}