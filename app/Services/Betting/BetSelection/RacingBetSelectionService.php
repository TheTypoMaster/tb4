<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 2:33 PM
 */

namespace TopBetta\Services\Betting\BetSelection;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class RacingBetSelectionService extends AbstractBetSelectionService {

    private $winProduct;

    private $placeProduct;

    /**
     * @inheritdoc
     */
    public function validateSelection($selection, $winDividend = 0, $placeDividend = 0)
    {
        //check selection is valid racing selection
        if( ! $this->selectionService->isSelectionRacing($selection->id) ) {
            throw new BetSelectionException($selection, "Invalid selction");
        }

        $this->validateFixedOdds($selection, $winDividend, $placeDividend);

        parent::validateSelection($selection);
    }

    public function validateFixedOdds($selection, $winDividend, $placeDividend)
    {
        //fixed odds validation
        if ($this->winProduct && $this->winProduct->is_fixed_odds) {
            if ($winDividend <= 0) {
                throw new BetSelectionException($selection, "Invalid dividend");
            }

            if ($selection->productPrice($this->winProduct->id)->win_odds != $winDividend) {
                throw new BetSelectionException($selection, "Price has changed");
            }
        }

        if ($this->placeProduct && $this->placeProduct->is_fixed_odds) {
            if ($placeDividend <= 0) {
                throw new BetSelectionException($selection, "Invalid dividend");
            }

            if ($selection->productPrice($this->placeProduct->id)->place_odds != $placeDividend) {
                throw new BetSelectionException($selection, "Price has changed");
            }
        }
    }

    /**
     * Check for fixed odds
     * @param $bet
     * @param $selection
     * @param array $extraData
     * @return mixed
     */
    public function createSelection($bet, $selection, $extraData = array())
    {
        $data = array();

        if ($bet['bet_type'] == BetTypeRepositoryInterface::TYPE_WIN && $this->winProduct->is_fixed_odds) {
            $data = array("fixed_odds" => $selection['win_dividend']);
        } else if ($bet['bet_type'] == BetTypeRepositoryInterface::TYPE_PLACE && $this->placeProduct->is_fixed_odds) {
            $data = array("fixed_odds" => $selection['place_dividend']);
        }

        return parent::createSelection($bet, $selection, array_merge($extraData, $data));
    }

    /**
     * @param mixed $winProduct
     * @return $this
     */
    public function setWinProduct($winProduct)
    {
        $this->winProduct = $winProduct;
        return $this;
    }

    /**
     * @param mixed $placeProduct
     * @return $this
     */
    public function setPlaceProduct($placeProduct)
    {
        $this->placeProduct = $placeProduct;
        return $this;
    }


}