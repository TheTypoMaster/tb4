<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:41 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class RacingEachWayBetPlacementService extends AbstractTournamentBetPlacementService {

    protected $selectionServiceClass = 'TopBetta\Services\Betting\BetSelection\RacingBetSelectionService';

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $bets = array();

        //get win and place bet types
        $betTypeRepository = \App::make('TopBetta\Repositories\Contracts\BetTypeRepositoryInterface');
        $winBet = $betTypeRepository->getBetTypeByName(BetTypeRepositoryInterface::TYPE_WIN);
        $placeBet = $betTypeRepository->getBetTypeByName(BetTypeRepositoryInterface::TYPE_PLACE);

        foreach($selections as $selection) {
            $bets[] = parent::createBet($ticket, array($selection), $amount, $winBet, $extraData);
            $bets[] = parent::createBet($ticket, array($selection), $amount, $placeBet, $extraData);
        }

        return $bets;
    }

    public function checkBetLimit($ticket, $selections, $amount, $betType)
    {
        $this->betLimitService->checkSingeSelectionBetLimit(
            $ticket,
            array_merge($selections, $selections),
            $amount,
            $betType
        );
    }

    public function getTotalAmountForBet($selections, $amount)
    {
        return count($selections) * $amount * 2;
    }
}