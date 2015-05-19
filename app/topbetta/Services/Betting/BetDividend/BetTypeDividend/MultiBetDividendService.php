<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 2:38 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


use TopBetta\Services\Betting\MultiBetService;

class MultiBetDividendService {

    public function getResultedDividendForBet($bet)
    {
        $winningSelections = $bet->selection->filter(function ($v) {
            return ! is_null( $v->result );
        });

        if ( $winningSelections->count() < MultiBetService::neededWinners($bet->type->name) ) {
            return 0;
        }

        return $this->calculateDividend($winningSelections, MultiBetService::neededWinners($bet->type->name));
    }

    public function calculateDividend($betSelections, $neededWinners, $winners = 0, $partialDividend = 1, $index = 0)
    {
        $totalDividend = 0;

        for( $i = $index; $i <= $betSelections->count() - ($neededWinners - $winners); $i++ ) {
            $selection = $betSelections->get($i);

            if( $neededWinners > $winners ) {
                $totalDividend += $this->calculateDividend($betSelections, $neededWinners, $winners + 1, $partialDividend * $selection->fixed->odds, $index + 1);
            } else {
                return $partialDividend * $selection->fixed_odds;
            }
        }

        return $totalDividend;
    }
}