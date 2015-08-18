<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 10:20 AM
 */

namespace TopBetta\Services\Betting\BetLiability;


class PlaceBetLiabilityCalculator extends AbstractLiabilityCalculator {

    public function getSelectionPrice($selection)
    {
        return object_get($selection['selection']->price, 'place_odds', 0);
    }

    public function getMaxWinners($market)
    {
        if ($market->selections->count() > 7) {
            return 3;
        } else if ($market->selections->count() > 4) {
            return 2;
        }

        return 0;
    }

    public function getBetLiability($bet)
    {
        return $bet->bet_amount * object_get($bet->selection->first()->price, 'place_odds', 0) - $bet->bet_freebet_amount;
    }
}