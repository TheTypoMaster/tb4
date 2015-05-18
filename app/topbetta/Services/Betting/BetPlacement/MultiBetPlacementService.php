<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 2:04 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\SportBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetPlacementException;
use TopBetta\Services\Betting\MultiBetService;
use TopBetta\Services\Risk\RiskSportsBetService;

class MultiBetPlacementService extends AbstractBetPlacementService {

    public function __construct(SportBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, RiskSportsBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount;
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        //TODO: MULTI BET LIMITS
        return true;
    }

    /**
     * @inheritdoc
     */
    public function validateBet($user, $amount, $type, $selections)
    {
        //TODO: REAL MULTI BET RULES
        if( count($selections) < MultiBetService::neededWinners($type) ) {
            throw new BetPlacementException("Not enough selections");
        }

        if( $amount < MultiBetService::calculateCombinations($type, $selections) ) {
            throw new BetPlacementException("Must have at least 100%");
        }

        if(count(array_fetch($selections, 'selection')) != count(array_unique(array_fetch($selections, 'selection')))) {
            throw new BetPlacementException("Duplicate Selections");
        }

        parent::validateBet($user, $amount, $type, $selections);
    }

    /**
     * @inheritdoc
     */
    public function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        //add the percentage and combinations
        $data = array(
            //TODO: flexi flag?
            "flexi_flag" => true,
            "percentage" => bcdiv(abs(array_get($transactions, 'account.amount', 0)) + abs(array_get($transactions, 'free_credit.amount', 0)), MultiBetService::calculateCombinations($type, $selections), 2),
            "combinations" => MultiBetService::calculateCombinations($type, $selections),
        );

        return parent::createBet($user, $transactions, $type, $origin, $selections, $data);
    }
}