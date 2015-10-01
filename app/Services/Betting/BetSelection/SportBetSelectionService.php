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

    /**
     * @inheritdoc
     */
    public function validateSelection($selection, $winDividend = 0, $placeDividend = 0)
    {
        //check selections is valid sports selection
        if ( ! $this->selectionService->isSelectionSports($selection->id) ) {
            throw new BetSelectionException($selection, "Invalid selection");
        }

        //make sure dividend is given
        if ( ! $winDividend ) {
            throw new BetSelectionException($selection, 'invalid dividend');
        }

        //check odds haven't changed
        if( $this->selectionService->oddsChanged($selection->id, $winDividend) ) {
            throw new BetSelectionException($selection, 'odds have changed');
        }

        if ( ! $this->marketService->isMarketOpen($selection->market) ) {
            throw new BetSelectionException($selection, 'Market is not open for betting');
        }

        if ($this->eventService->eventPastStartTime($selection->market->event) ) {
            throw new BetSelectionException($selection, 'Event is past start time');
        }

        parent::validateSelection($selection);
    }

    /**
     * @inheritdoc
     */
    public function createSelection($bet, $selection, $extraData = array())
    {
        //add the fixed odds
        $data = array(
            'fixed_odds' => $selection['win_dividend']
        );

        return parent::createSelection($bet, $selection, $data);
    }
}