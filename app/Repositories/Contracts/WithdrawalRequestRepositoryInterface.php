<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 11:35 AM
 */
namespace TopBetta\Repositories\Contracts;

interface WithdrawalRequestRepositoryInterface
{
    public function getTotalWithdrawalsForUserWithApproved($userId, $approved);
}