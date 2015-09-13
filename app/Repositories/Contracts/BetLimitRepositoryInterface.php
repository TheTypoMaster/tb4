<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/06/2015
 * Time: 4:09 PM
 */
namespace TopBetta\Repositories\Contracts;

interface BetLimitRepositoryInterface
{
    public function getLimitForUserAndBetType($user, $betType, $limitType = 'bet_type');
}