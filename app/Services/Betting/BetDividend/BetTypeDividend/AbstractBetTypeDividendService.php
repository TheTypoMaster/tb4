<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:40 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


abstract class AbstractBetTypeDividendService {

    /**
     * Gets the dividend for a resulted bet
     * @param $bet
     * @return float
     */
    abstract public function getResultedDividendForBet($bet);
}