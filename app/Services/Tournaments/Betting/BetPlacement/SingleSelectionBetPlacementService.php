<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:32 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


class SingleSelectionBetPlacementService extends AbstractTournamentBetPlacementService {

    public function checkBetLimit($ticket, $selections, $amount, $betType)
    {
        $this->betLimitService->checkSingeSelectionBetLimit($ticket, $selections, $amount);
    }

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $bets = array();

        foreach ($selections as $selection) {
            $bets[] = parent::createBet($ticket, array($selection), $amount, $betType, $extraData);
        }

        return $bets;
    }

    public function getTotalAmountForBet($selections, $amount)
    {
        return count($selections) * $amount;
    }
}