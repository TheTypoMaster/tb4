<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 1:51 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Exceptions;


class BetExposureLimitExceeedException extends BetLimitExceededException {

    public function constructMessage()
    {
        return \Lang::get('bets.exceed_bet_limit_exposure', array('exposure' => number_format($this->limit/100, 2)));
    }
}