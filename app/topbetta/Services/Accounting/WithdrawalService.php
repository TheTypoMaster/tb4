<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 11:28 AM
 */

namespace TopBetta\Services\Accounting;


use TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface;

class WithdrawalService {

    /**
     * @var WithdrawalRequestRepositoryInterface
     */
    private $withdrawalRequestRepository;

    public function __construct(WithdrawalRequestRepositoryInterface $withdrawalRequestRepository)
    {
        $this->withdrawalRequestRepository = $withdrawalRequestRepository;
    }

    /**
     * Gets the sum of the total approved withdrawals for a user
     * @param $userId
     * @return mixed
     */
    public function getTotalApprovedWithdrawalsForUser($userId)
    {
        return $this->withdrawalRequestRepository->getTotalWithdrawalsForUserWithApproved($userId, true);
    }
}