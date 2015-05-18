<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 1:21 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\SportBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Risk\RiskSportsBetService;

class SportBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(SportBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, RiskSportsBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            $exceedLimit = $this->betLimitRepo->checkExceedBetLimitForBetData(array(
                'id'          => $selection->market->event->id,
                'selection'   => $selection->id,
                'bet_type_id' => $this->betTypeRepository->getBetTypeByName($betType)->id,
                'value'       => $amount,
            ), 'sport');

            if( $exceedLimit['result'] ) {
                return false;
            }
        }

        return true;
    }
}