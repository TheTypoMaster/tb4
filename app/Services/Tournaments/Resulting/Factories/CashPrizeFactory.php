<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/08/2015
 * Time: 4:18 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


class CashPrizeFactory implements TournamentPrizeFactory {

    public static function make(array $data)
    {
        $prize = \App::make('TopBetta\Services\Tournaments\Resulting\CashPrize');

        $prize->setTicket(array_get($data, 'ticket'));
        $prize->setAmount(array_get($data, 'amount'));

        return $prize;
    }
}