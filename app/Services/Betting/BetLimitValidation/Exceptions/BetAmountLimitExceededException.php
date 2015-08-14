<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 3:01 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Exceptions;


class BetAmountLimitExceededException extends BetLimitExceededException {

    public function constructMessage()
    {
        return \Lang::get('bets.exceed_bet_limit_value', array('betValueLimit' => number_format($this->limit/100, 2)));
    }
}