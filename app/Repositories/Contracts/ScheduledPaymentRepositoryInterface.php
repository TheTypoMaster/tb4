<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 11:29 AM
 */
namespace TopBetta\Repositories\Contracts;

use Config;

interface ScheduledPaymentRepositoryInterface
{
    public function getPaymentsDueAfterDate($date);

    public function getActivePaymentsForUser($userId, $source = null);
}