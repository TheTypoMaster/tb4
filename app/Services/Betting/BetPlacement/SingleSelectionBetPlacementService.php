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
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\Betting\BetLimitValidation\BetLimitValidationService;
use TopBetta\Services\Betting\BetSelection\AbstractBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Risk\AbstractRiskBetService;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

/**
 * Base class for single sports and racing bets
 * Multiple selections means multiple bets
 * Class SingleSelectionBetPlacementService
 * @package TopBetta\Services\Betting\BetPlacement
 */
abstract class SingleSelectionBetPlacementService extends AbstractBetPlacementService {

    public function __construct(AbstractBetSelectionService $betSelectionService, BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitValidationService $betLimitService, AbstractRiskBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitService, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        $bets = array();

        foreach($selections as $selection) {
            $bets[] = parent::_placeBet($user, $amount, $type, $origin, array($selection), $freeCreditFlag);
        }

        return $bets;
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount * count($selections);
    }

    /**
     * @inheritdoc
     */
    protected function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        $data = array(
            'event_id' => $selections[0]['selection']->market->event->id,
        );

        return parent::createBet($user, $transactions, $type, $origin, $selections, array_merge($data, $extraData));
    }
}