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
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\Betting\BetProduct\BetProductValidator;
use TopBetta\Services\Betting\BetSelection\RacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Risk\RiskRacingWinPlaceBetService;

class RacingEachWayBetPlacementService extends SingleSelectionBetPlacementService {

    private $winProduct;

    private $placeProduct;

    protected $product;

    public function __construct(RacingBetSelectionService $betSelectionService,
                                BetTransactionService $betTransactionService,
                                BetRepositoryInterface $betRepository,
                                BetTypeRepositoryInterface $betTypeRepository,
                                BetLimitService $betLimitService,
                                RiskRacingWinPlaceBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitService, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount * count($selections) * 2;
    }

    /**
     * @inheritdoc
     */
    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        $bets = array();

        foreach($selections as $selection) {
            //win bet
            $this->setProduct($this->winProduct);
            $bets[] = parent::_placeBet($user, $amount, BetTypeRepositoryInterface::TYPE_WIN, $origin, array($selection), $freeCreditFlag)[0];

            //place bet
            $this->setProduct($this->placeProduct);
            $bets[] = parent::_placeBet($user, $amount, BetTypeRepositoryInterface::TYPE_PLACE, $origin, array($selection), $freeCreditFlag)[0];
        }

        return $bets;
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            //check limits for both win and place
            foreach(array(BetTypeRepositoryInterface::TYPE_WIN, BetTypeRepositoryInterface::TYPE_PLACE) as $type) {
                $exceedLimit = $this->betLimitService->getWinPlaceBetLimitExceeded(
                    $user,
                    $amount,
                    $selection['selection'],
                    $this->betTypeRepository->getBetTypeByName($type)->id
                );

                if ($exceedLimit) {
                    throw new BetLimitExceededException(array('betValueLimit' => $exceedLimit), $selection);
                }
            }
        }
    }

    /**
     * Validate product
     * @param $user
     * @param $amount
     * @param $type
     * @param $selections
     */
    public function validateBet($user, $amount, $type, $selections)
    {
        parent::validateBet($user, $amount, $type, $selections);

        $meetings = array_unique(array_map(function ($v) {
            return $v->market->event->competition->first();
        }, array_pluck($selections, 'selection')));

        foreach ($meetings as $meeting) {
            $validator = BetProductValidator::make($meeting);
            $validator->validateProduct($this->winProduct, BetTypeRepositoryInterface::TYPE_WIN);
            $validator->validateProduct($this->placeProduct, BetTypeRepositoryInterface::TYPE_PLACE);
        }
    }

    /**
     * @param mixed $winProduct
     * @return $this
     */
    public function setWinProduct($winProduct)
    {
        $this->winProduct = $winProduct;

        $this->betSelectionService->setWinProduct($winProduct);

        return $this;
    }

    /**
     * @param mixed $placeProduct
     * @return $this
     */
    public function setPlaceProduct($placeProduct)
    {
        $this->placeProduct = $placeProduct;

        $this->betSelectionService->setPlaceProduct($placeProduct);

        return $this;
    }


}