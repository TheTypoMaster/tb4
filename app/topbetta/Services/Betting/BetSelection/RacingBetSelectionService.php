<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 2:33 PM
 */

namespace TopBetta\Services\Betting\BetSelection;


use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class RacingBetSelectionService extends AbstractBetSelectionService {


    public function validateSelection($selection, $dividend = 0)
    {
        //TODO: MESSAGE!
        if( ! $this->selectionService->isSelectionRacing($selection->id) ) {
            throw new BetSelectionException($selection, "");
        }

        parent::validateSelection($selection);

    }
}