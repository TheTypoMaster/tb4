<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/02/2015
 * Time: 3:47 PM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionStatusRepositoryInterface;

class SelectionService {

    //do in DB
    const SELECTION_NOT_SCRATCHED = "not scratched";

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var SelectionStatusRepositoryInterface
     */
    private $selectionStatusRepository;

    public function __construct(SelectionRepositoryInterface $selectionRepository, SelectionStatusRepositoryInterface $selectionStatusRepository)
    {
        $this->selectionRepository = $selectionRepository;
        $this->selectionStatusRepository = $selectionStatusRepository;
    }

    /**
     * Gets the selection model by id
     * @param $selectionId
     * @return mixed
     */
    public function getSelection($selectionId)
    {
        return $this->selectionRepository->find($selectionId);
    }

    /**
     * Checks if selection is open and not scratched
     * @param $selection
     * @return bool
     */
    public function isSelectionAvailableForBetting($selection)
    {
        if( is_int($selection ) ) {
            $selection = $this->getSelection($selection);
        }

        if( $selection->selection_status_id == $this->selectionStatusRepository->getSelectionStatusIdByName(self::SELECTION_NOT_SCRATCHED) && $selection->display_flag ) {
            return true;
        }

        return false;
    }

    public function calculatePrice($selectionPrice, $overrideOdds, $overrideType)
    {
        if ($overrideType == 'percentage') {
            return bcmul(2 - $overrideOdds, $selectionPrice, 2);
        } else if ($overrideType == 'promo') {
            return $overrideOdds;
        } else if ($overrideType == 'price') {
            return min($selectionPrice, $overrideOdds);
        }

        return $selectionPrice;
    }

    public function calculatePriceForSelection($selectionId)
    {
        $selection = $this->selectionRepository->find($selectionId);

        return $this->calculatePrice($selection->price->win_odds, $selection->price->override_odds, $selection->price->override_type);
    }

    public function oddsChanged($selectionId, $price)
    {
        return $this->calculatePriceForSelection($selectionId) != $price;
    }

}