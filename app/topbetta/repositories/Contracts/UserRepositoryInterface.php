<?php namespace TopBetta\Repositories\Contracts;
use Carbon\Carbon;

/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:14
 * Project: tb4
 */


interface UserRepositoryInterface {

    public function getDormantUsersWithNoDormantChargeAfter($dormantTransactionType, $days, $chargeDate);
}