<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 11:28 AM
 */

namespace TopBetta\Repositories;

use TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface;
use TopBetta\WithdrawalRequest;

class DbWithdrawalRequestRepository extends BaseEloquentRepository implements WithdrawalRequestRepositoryInterface
{

    public function  __construct(WithdrawalRequest $withdrawalRequest)
    {
        $this->model = $withdrawalRequest;
    }

    /**
     * Gets the sum of the withdrawals for a user based on whether it was approved or not.
     * @param $userId
     * @param $approved
     * @return mixed
     */
    public function getTotalWithdrawalsForUserWithApproved($userId, $approved)
    {
        return $this
            ->model
            ->where('requester_id', '=', $userId)
            ->where('approved_flag', '=', (int) $approved)
            ->sum('amount');
    }
}