<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:43 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


class PlaceBetDividendService extends AbstractBetDividendService {

    public function getResultedDividendForBet($bet)
    {
        if( $bet->selection->result && $bet->selection->result->position <= 3 ) {
            return $bet->selection->result->place_odds;
        }

        return 0;
    }
}