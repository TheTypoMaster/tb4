<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 3:03 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Exceptions;


class BetFlexiLimitExceededException extends BetLimitExceededException {

    public function constructMessage()
    {
        return \Lang::get("bets.exceed_bet_limit_flexi", array("flexiLimit" => $this->limit));
    }
}