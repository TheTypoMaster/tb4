<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 12:27 PM
 */

namespace TopBetta\Services\Betting\BetLiability;


use TopBetta\Repositories\Contracts\BetRepositoryInterface;

class SportBetLiabilityCalculator extends AbstractLiabilityCalculator {

    protected function getSelectionPrice($selection)
    {
        return $selection['dividend'];
    }

    protected function getBetLiability($bet)
    {
        return $bet->bet_amount * $bet->betselection->first()->fixed_odds - $bet->bet_freebet_amount;
    }

     protected function getMaxWinners($market)
     {
        return object_get($market->markettype->sportDetails($market->event->competition->first()->sport), 'max_winning_selections', 1);
     }
}

