<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 12:17 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\AbstractBetSelectionService;

abstract class SingleSelectionBetPlacementService extends AbstractBetPlacementService {

    public function __construct(AbstractBetSelectionService $betSelectionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository)
    {
        parent::__construct($betSelectionService, $betRepository, $betTypeRepository);
    }

    public function placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        $bets = array();

        foreach($selections as $selection) {
            $bets[] = $this->_placeBet($user, $amount, $type, $origin, $selection, $freeCreditFlag);
        }

        return $bets;
    }

}