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

    public function userTransactionHistoryByNameDOB($users)
    {
        $data = array();
        foreach($users as $name) {
            //get the user
            $user = $this->userAccountService->findUserByNameAndDob($name['first_name'], $name['last_name'], $name['dob']);

            //user exists so get transaction history
            if( $user ) {
                $data = array_merge($data, $this->activityService->userTransactionHistory($user));
            }
        }

        return $data;
    }
}