<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:47 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


class SportBetPlacementService extends SingleSelectionBetPlacementService {

    protected $selectionServiceClass = 'TopBetta\Services\Betting\BetSelection\SportBetSelectionService';

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $bets = array();

        foreach($selections as $selection) {
            $bets[] = parent::createBet($ticket, array($selection), $amount, $betType, array("fixed_odds" => $selection['dividend']));
        }

        return $bets;
    }
}