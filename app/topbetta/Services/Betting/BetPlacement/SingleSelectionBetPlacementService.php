<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 12:17 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\AbstractBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Risk\AbstractRiskBetService;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

abstract class SingleSelectionBetPlacementService extends AbstractBetPlacementService {

    public function __construct(AbstractBetSelectionService $betSelectionService, BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, AbstractRiskBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        $bets = array();

        foreach($selections as $selection) {
            $bets[] = parent::_placeBet($user, $amount, $type, $origin, array($selection), $freeCreditFlag);
        }

        return $bets;
    }

    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount * count($selections);
    }

    protected function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        $data = array(
            'event_id' => $selections[0]['selection']->market->event->id,
        );

        return parent::createBet($user, $transactions, $type, $origin, $selections, array_merge($extraData, $data));
    }
}