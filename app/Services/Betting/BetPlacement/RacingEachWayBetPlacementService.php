<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 4:58 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;

use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\Betting\BetSelection\RacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

class RacingEachWayBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(RacingBetSelectionService $betSelectionService,
                                BetTransactionService $betTransactionService,
                                BetRepositoryInterface $betRepository,
                                BetTypeRepositoryInterface $betTypeRepository,
                                BetLimitService $betLimitService,
                                RiskRacingWinPlaceBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitService, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount * count($selections) * 2;
    }

    /**
     * @inheritdoc
     */
    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        $bets = array();

        foreach($selections as $selection) {
            //win bet
            $bets[] = parent::_placeBet($user, $amount, BetTypeRepositoryInterface::TYPE_WIN, $origin, array($selection), $freeCreditFlag)[0];

            //place bet
            $bets[] = parent::_placeBet($user, $amount, BetTypeRepositoryInterface::TYPE_PLACE, $origin, array($selection), $freeCreditFlag)[0];
        }

        return $bets;
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            //check limits for both win and place
            foreach(array(BetTypeRepositoryInterface::TYPE_WIN, BetTypeRepositoryInterface::TYPE_PLACE) as $type) {
                $exceedLimit = $this->betLimitService->getWinPlaceBetLimitExceeded(
                    $user,
                    $amount,
                    $selection['selection'],
                    $this->betTypeRepository->getBetTypeByName($type)->id
                );

                if ($exceedLimit) {
                    throw new BetLimitExceededException(array('betValueLimit' => $exceedLimit), $selection);
                }
            }
        }
    }
}