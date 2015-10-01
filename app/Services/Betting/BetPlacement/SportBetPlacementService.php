<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 1:21 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;

use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\Betting\BetLimitValidation\BetLimitValidationService;
use TopBetta\Services\Betting\BetSelection\SportBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Risk\RiskSportsBetService;

class SportBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(SportBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitValidationService $betLimitService, RiskSportsBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitService, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        $betLimitData = array(
            'amount' => $amount,
            'user' => $user->id,
            'bet_type' => $this->betTypeRepository->getBetTypeByName($betType),
            'selections' => $selections,
            'product' => null,
        );

        $this->betLimitService->validateBet($betLimitData);
    }
}