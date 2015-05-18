<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 1:19 PM
 */

namespace TopBetta\Services\Betting\BetSelection;


use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class SportBetSelectionService extends AbstractBetSelectionService {

    public function validateSelection($selection, $dividend = 0)
    {
        if ( ! $this->selectionService->isSelectionSports($selection->id) ) {
            throw new BetSelectionException($selection, "Invalid selection");
        }

        if ( ! $dividend ) {
            throw new BetSelectionException($selection, 'invalid dividend');
        }

        if( $this->selectionService->oddsChanged($selection->id, $dividend) ) {
            throw new BetSelectionException($selection, 'odds have changed');
        }

        parent::validateSelection($selection);
    }

    public function createSelection($bet, $selection, $extraData = array())
    {
        $data = array(
            'fixed_odds' => $selection['dividend']
        );

        return parent::createSelection($bet, $selection, $data);
    }
}