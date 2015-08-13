<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/05/2015
 * Time: 4:55 PM
 */

namespace TopBetta\Services\UserAccount;


class UserReportService {

    /**
     * @var UserAccountService
     */
    private $userAccountService;
    /**
     * @var UserActivityService
     */
    private $activityService;

    public function __construct(UserAccountService $userAccountService, UserActivityService $activityService)
    {
        $this->userAccountService = $userAccountService;
        $this->activityService = $activityService;
    }

    public function userTransactionHistoryByNameDOB($firstName, $lastName, $dob, $email = null)
    {
        $data = null;

        //get the user
        $user = $this->userAccountService->findUserByNameAndDob($firstName, $lastName, $dob);

        if (!$user) {
            $user = $this->userAccountService->findFullUserByEmail($email);

        }

        //user exists so get transaction history
        if( $user ) {
            $data = $this->activityService->userTransactionHistory($user);
        }

        return $data;
    }
}