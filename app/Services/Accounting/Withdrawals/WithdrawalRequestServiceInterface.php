<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 11:56 AM
 */

namespace TopBetta\Services\Accounting\Withdrawals;


interface WithdrawalRequestServiceInterface {

    public function processRequest($user, $request);
}