<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 2:38 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


use TopBetta\Services\Betting\MultiBetService;

class MultiBetTypeDividendService extends AbstractBetTypeDividendService {

    public function getResultedDividendForBet($bet)
    {
        //get the winning selections and reset the keys
        $winningSelections = $bet->betselection->filter(function ($v) {
            return ! is_null( $v->selection->result );
        })->values();

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

            if( $neededWinners > $winners + 1) {
                $totalDividend += $this->calculateDividend($betSelections, $neededWinners, $winners + 1, $partialDividend * $selection->fixed_odds, $i + 1);
            } else {
                $totalDividend += $partialDividend * $selection->fixed_odds;
            }
        }

        return $totalDividend;
    }
}