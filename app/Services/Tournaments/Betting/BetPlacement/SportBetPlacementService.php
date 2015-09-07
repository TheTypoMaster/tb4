<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:47 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


class SportBetPlacementService extends AbstractTournamentBetPlacementService {

    protected $selectionServiceClass = 'TopBetta\Services\Betting\BetSelection\SportBetSelectionService';

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $bets = array();

        foreach($selections as $selection) {
            $bets[] = parent::createBet($ticket, array($selection), $amount, $betType, array("fixed_odds" => $selection['win_dividend']));
        }

        return $bets;
    }

    public function checkBetLimit($ticket, $selections, $amount, $betType)
    {
        $this->betLimitService->checkSingeSelectionBetLimit($ticket, $selections, $amount);
    }

    public function getTotalAmountForBet($selections, $amount)
    {
        return count($selections) * $amount;
    }
}