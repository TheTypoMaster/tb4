<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 4:58 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\RacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

class RacingEachWayBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(RacingBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, RiskRacingWinPlaceBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount * count($selections) * 2;
    }

    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        $bets = array();

        foreach($selections as $selection) {
            //win bet
            $bets[] = parent::_placeBet($user, $amount, BetTypeRepositoryInterface::TYPE_WIN, $origin, array($selection), $freeCreditFlag);

            //place bet
            $bets[] = parent::_placeBet($user, $amount, BetTypeRepositoryInterface::TYPE_PLACE, $origin, array($selection), $freeCreditFlag);
        }

        return $bets;
    }

    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            foreach(array(BetTypeRepositoryInterface::TYPE_WIN, BetTypeRepositoryInterface::TYPE_PLACE) as $type) {
                $exceedLimit = $this->betLimitRepo->checkExceedBetLimitForBetData(array(
                    'id'          => $selection->market->event->id,
                    'selection'   => $selection->id,
                    'bet_type_id' => $this->betTypeRepository->getBetTypeByName($type)->id,
                    'value'       => $amount,
                ), 'racing');

                if ($exceedLimit['result']) {
                    throw new BetLimitExceededException($exceedLimit, $selection);
                }
            }
        }
    }
}