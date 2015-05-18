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
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

class RacingWinPlaceBetPlacementService extends SingleSelectionBetPlacementService {

    public function __construct(RacingBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, RiskRacingWinPlaceBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            $exceedLimit = $this->betLimitRepo->checkExceedBetLimitForBetData(array(
                'id'          => $selection['selection']->market->event->id,
                'selection'   => $selection['selection']->id,
                'bet_type_id' => $this->betTypeRepository->getBetTypeByName($betType)->id,
                'value'       => $amount,
            ), 'racing');

            if( $exceedLimit['result'] ) {
                throw new BetLimitExceededException($exceedLimit);
            }
        }

    }
}