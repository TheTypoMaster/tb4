<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 4:48 PM
 */
namespace TopBetta\Repositories\Contracts;

interface BetResultStatusRepositoryInterface
{
    const RESULT_STATUS_UNRESULTED = 'unresulted';
    const RESULT_STATUS_PAID = 'paid';

    public function getByName($name);
}