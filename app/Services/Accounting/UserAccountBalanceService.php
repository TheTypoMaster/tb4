<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/05/2015
 * Time: 12:31 PM
 */

namespace TopBetta\Services\Accounting;


class UserAccountBalanceService {

    /**
     * Checks if the user has enough money in account to cover $amount
     * @param $user \User | \TopBetta\Models\UserModel
     * @param int $amount
     * @param bool $useFreeCredit
     * @return bool
     */
    public static function hasSufficientFunds($user, $amount, $useFreeCredit = false)
    {
        if ( $useFreeCredit ) {
            //free credit so check user has enough free credit or account balance to cover
            $freeCreditBalance = $user->freeCreditBalance();

            if( $freeCreditBalance >= $amount ) { return true; }
            else if ($freeCreditBalance + $user->accountBalance() >= $amount ) { return true; }

        } else {
            //not free credit so just check account balance
            if( $user->accountBalance() > $amount ) { return true; }
        }

        return false;
    }
}