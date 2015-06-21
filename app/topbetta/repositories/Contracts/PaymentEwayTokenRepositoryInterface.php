<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 1:00 PM
 */
namespace TopBetta\Repositories\Contracts;

interface PaymentEwayTokenRepositoryInterface
{
    public function getByToken($token);
}