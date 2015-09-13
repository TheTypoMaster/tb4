<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 12:42 PM
 */

namespace TopBetta\Services\Accounting\Withdrawals\Factories;


use TopBetta\Repositories\Contracts\WithdrawalTypeRepositoryInterface;

class WithdrawalRequestServiceFactory {

    /**
     * @param $type
     * @return \TopBetta\Services\Accounting\Withdrawals\WithdrawalRequestServiceInterface
     */
    public static function make($type)
    {
        switch($type) {
            case WithdrawalTypeRepositoryInterface::WITHDRAWAL_TYPE_BANK:
                return \App::make('TopBetta\Services\Accounting\Withdrawals\BankWithdrawalRequestService');
            case WithdrawalTypeRepositoryInterface::WITHDRAWAL_TYPE_PAYPAL:
                return \App::make('TopBetta\Services\Accounting\Withdrawals\PayPalWithdrawalRequestService');
        }

        throw new \InvalidArgumentException("Withdrawal type " . $type . " does not exist");
    }
}