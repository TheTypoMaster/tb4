<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 3:12 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;

use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\Betting\BetProduct\BetProductValidator;
use TopBetta\Services\Betting\BetSelection\RacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

class RacingWinPlaceBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(RacingBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitService $betLimitService, RiskRacingWinPlaceBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitService, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            $exceedLimit = $this->betLimitService->getWinPlaceBetLimitExceeded(
                $user,
                $amount,
                $selection['selection'],
                $this->betTypeRepository->getBetTypeByName($betType)->id
            );

            if( $exceedLimit ) {
                throw new BetLimitExceededException(array('betValueLimit' => $exceedLimit));
            }
        }

    }

    /**
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        $this->betType == BetTypeRepositoryInterface::TYPE_PLACE ?
            $this->betSelectionService->setPlaceProduct($product) :
            $this->betSelectionService->setWinProduct($product);

        return $this;
    }

    /**
     * Validate product
     * @param $user
     * @param $amount
     * @param $type
     * @param $selections
     */
    public function validateBet($user, $amount, $type, $selections)
    {
        parent::validateBet($user, $amount, $type, $selections);

        $meetings = array_unique(array_map(function ($v) {
            return $v->market->event->competition->first();
        }, array_pluck($selections, 'selection')));

        foreach ($meetings as $meeting) {
            $validator = BetProductValidator::make($meeting);
            $validator->validateProduct($this->product, $this->betType);
        }
    }


}