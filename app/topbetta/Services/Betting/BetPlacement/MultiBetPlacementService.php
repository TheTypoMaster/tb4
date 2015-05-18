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
use TopBetta\Services\Betting\MultiBetService;
use TopBetta\Services\Risk\RiskSportsBetService;

class MultiBetPlacementService extends AbstractBetPlacementService {

    public function __construct(SportBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, RiskSportsBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount;
    }

    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        //TODO: MULTI BET LIMITS
        return true;
    }

    public function isBetValid($user, $amount, $type, $selections)
    {
        //TODO: REAL MULTI BET RULES
        if( count($selections) < MultiBetService::neededWinners($type) ) {
            return false;
        }

        if( $amount < MultiBetService::calculateCombinations($type, $selections) ) {
            return false;
        }

        if(count($selections) != count(array_unique($selections))) {
            return false;
        }

        return parent::isBetValid($user, $amount, $type, $selections);
    }

    public function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        $data = array(
            //TODO: flexi flag?

            "percentage" => bcdiv(abs(array_get($transactions, 'account.amount', 0)) + abs(array_get($transactions, 'free_credit.amount', 0)), MultiBetService::calculateCombinations($type, $selections), 2),
            "combinations" => MultiBetService::calculateCombinations($type, $selections),
        );

        return parent::createBet($user, $transactions, $type, $origin, $selections, $data);
    }
}