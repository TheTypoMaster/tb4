<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:41 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Tournaments\Betting\BetProduct\TournamentBetProductValidator;

class RacingEachWayBetPlacementService extends AbstractTournamentBetPlacementService {

    protected $selectionServiceClass = 'TopBetta\Services\Betting\BetSelection\RacingBetSelectionService';

    protected $winProduct;

    protected $placeProduct;

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $bets = array();

        //get win and place bet types
        $betTypeRepository = \App::make('TopBetta\Repositories\Contracts\BetTypeRepositoryInterface');
        $winBet = $betTypeRepository->getBetTypeByName(BetTypeRepositoryInterface::TYPE_WIN);
        $placeBet = $betTypeRepository->getBetTypeByName(BetTypeRepositoryInterface::TYPE_PLACE);

        foreach($selections as $selection) {
            $this->setProduct($this->winProduct);
            $data = array("fixed_odds" => $this->product->is_fixed_odds ? $selections[0]['win_dividend'] : null);
            $bets[] = parent::createBet($ticket, array($selection), $amount, $winBet, array_merge($extraData, $data));

            $this->setProduct($this->placeProduct);
            $data = array("fixed_odds" => $this->product->is_fixed_odds ? $selections[0]['place_dividend'] : null);
            $bets[] = parent::createBet($ticket, array($selection), $amount, $placeBet, array_merge($extraData, $data));
        }

        return $bets;
    }

    public function checkBetLimit($ticket, $selections, $amount, $betType)
    {
        $this->betLimitService->checkSingeSelectionBetLimit(
            $ticket,
            array_merge($selections, $selections),
            $amount,
            $betType
        );
    }

    public function getTotalAmountForBet($selections, $amount)
    {
        return count($selections) * $amount * 2;
    }

    /**
     * Validate product
     * @param $user
     * @param $amount
     * @param $type
     * @param $selections
     */
    public function validateTournamentBet($user, $amount, $type, $selections)
    {
        parent::validateTournamentBet($user, $amount, $type, $selections);

        $meetings = array_unique(array_map(function ($v) {
            return $v->market->event->competition->first();
        }, array_pluck($selections, 'selection')));

        foreach ($meetings as $meeting) {
            $validator = TournamentBetProductValidator::make($meeting);
            $validator->validateProduct($this->winProduct, BetTypeRepositoryInterface::TYPE_WIN);
            $validator->validateProduct($this->placeProduct, BetTypeRepositoryInterface::TYPE_PLACE);
        }
    }



    /**
     * @param mixed $placeProduct
     * @return $this
     */
    public function setPlaceProduct($placeProduct)
    {
        $this->placeProduct = $placeProduct;
        $this->selectionService->setPlaceProduct($placeProduct);
        return $this;
    }

    /**
     * @param mixed $winProduct
     * @return $this
     */
    public function setWinProduct($winProduct)
    {
        $this->winProduct = $winProduct;
        $this->selectionService->setWinProduct($winProduct);
        return $this;
    }


}