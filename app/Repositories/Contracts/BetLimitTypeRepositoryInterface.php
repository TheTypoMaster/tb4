<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/06/2015
 * Time: 4:07 PM
 */
namespace TopBetta\Repositories\Contracts;

interface BetLimitTypeRepositoryInterface
{
    const BET_LIMIT_DEFAULT        = 'default';
    const BET_LIMIT_FLEXI_DEFAULT  = 'default_flexi';
    const BET_LIMIT_SPORT_DEFAULT  = 'default_sport';
    const BET_LIMIT_RACING         = 'bet_type';
    const BET_LIMIT_FLEXI          = 'bet_flexi';
    const BET_LIMIT_SPORT          = 'bet_sports';

    public function getByName($name);

    public function getLimitForUser($user, $betType, $limitType);
}