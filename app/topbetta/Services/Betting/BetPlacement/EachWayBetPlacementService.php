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
use TopBetta\Services\Betting\BetSelection\RacingWinPlaceBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;

class EachWayBetPlacementService extends AbstractBetPlacementService {

    public function __construct(RacingWinPlaceBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository);
    }
}