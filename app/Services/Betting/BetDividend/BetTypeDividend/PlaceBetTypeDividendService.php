<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:43 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


class PlaceBetTypeDividendService extends AbstractBetTypeDividendService {

    /**
     * @inheritdoc
     */
    public function getResultedDividendForBet($bet)
    {
        if( $bet->selection->first()->result && $bet->selection->first()->result->position <= 3 ) {
            return $bet->selection->first()->result->place_dividend;
        }

        return 0;
    }
}