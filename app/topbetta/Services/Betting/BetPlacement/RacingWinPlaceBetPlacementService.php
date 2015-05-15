<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 3:12 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\RacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;

class RacingWinPlaceBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(RacingBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo);
    }

    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            $exceedLimit = $this->betLimitRepo->checkExceedBetLimitForBetData(array(
                'id'          => $selection->market->event->id,
                'selection'   => $selection->id,
                'bet_type_id' => $this->betTypeRepository->getBetTypeByName($betType)->id,
                'value'       => $amount,
            ), 'racing');

            if( $exceedLimit['result'] ) {
                return false;
            }
        }

        return true;
    }
}