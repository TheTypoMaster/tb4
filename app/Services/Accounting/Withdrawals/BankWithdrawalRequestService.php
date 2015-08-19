<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Services\Accounting\Withdrawals;

use TopBetta\Repositories\Contracts\WithdrawalTypeRepositoryInterface;

class BankWithdrawalRequestService extends AbstractWithdrawalRequestService implements WithdrawalRequestServiceInterface {

    public function getType()
    {
        return WithdrawalTypeRepositoryInterface::WITHDRAWAL_TYPE_BANK;
    }
}