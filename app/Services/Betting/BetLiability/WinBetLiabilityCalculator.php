<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 10:17 AM
 */

namespace TopBetta\Services\Betting\BetLiability;


class WinBetLiabilityCalculator extends AbstractLiabilityCalculator implements LiabilityCalculator {

    public function getSelectionPrice($selection)
    {
        return object_get($selection['selection']->price, 'win_odds', 0);
    }

    public function getMaxWinners($market)
    {
        return 1;
    }

    public function getBetLiability($bet)
    {
        return $bet->bet_amount * object_get($bet->selection->first()->price, 'win_odds', 0) - $bet->bet_freebet_amount;
    }
}