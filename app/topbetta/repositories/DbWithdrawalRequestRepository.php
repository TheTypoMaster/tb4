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


    public function getTotalWithdrawalsForUserWithApproved($userId, $approved)
    {
        return $this
            ->model
            ->where('request_id', '=', $userId)
            ->where('approved_flag', '=', (int) $status)
            ->sum('amount');
    }
}