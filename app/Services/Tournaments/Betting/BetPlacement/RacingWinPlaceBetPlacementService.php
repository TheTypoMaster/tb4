<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:37 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class RacingWinPlaceBetPlacementService extends SingleSelectionBetPlacementService {

    protected $selectionServiceClass = 'TopBetta\Services\Betting\BetSelection\RacingBetSelectionService';
}