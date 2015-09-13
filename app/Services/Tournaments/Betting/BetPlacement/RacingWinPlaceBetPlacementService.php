<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:37 PM
 */

namespace TopBetta\Services\Tournaments\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Tournaments\Betting\BetProduct\TournamentBetProductValidator;

class RacingWinPlaceBetPlacementService extends SingleSelectionBetPlacementService {

    protected $selectionServiceClass = 'TopBetta\Services\Betting\BetSelection\RacingBetSelectionService';

    /**
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        $this->betType == BetTypeRepositoryInterface::TYPE_PLACE ?
            $this->selectionService->setPlaceProduct($product) :
            $this->selectionService->setWinProduct($product);

        return $this;
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
            $validator->validateProduct($this->product, $this->betType);
        }
    }

    public function createBet($ticket, $selections, $amount, $betType, $extraData = array())
    {
        $data = array();

        if ($this->product->is_fixed_odds) {
            $dividend = $this->betType == BetTypeRepositoryInterface::TYPE_WIN ? 'win_dividend' : 'place_dividend';
            $data = array("fixed_odds" => $this->product->is_fixed_odds ? $selections[0][$dividend] : null);
        }

        return parent::createBet($ticket, $selections, $amount, $betType, array_merge($data, $extraData));
    }
}