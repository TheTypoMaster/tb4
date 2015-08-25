<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 12:07 PM
 */
namespace TopBetta\Repositories\Contracts;

interface WithdrawalTypeRepositoryInterface
{
    const WITHDRAWAL_TYPE_BANK   = 'bank';
    const WITHDRAWAL_TYPE_PAYPAL = 'paypal';

    public function getByName($name);
}