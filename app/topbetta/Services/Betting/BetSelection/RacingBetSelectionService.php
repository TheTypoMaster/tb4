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

    /**
     * @inheritdoc
     */
    public function validateSelection($selection, $dividend = 0)
    {
        //check selection is valid racing selection
        if( ! $this->selectionService->isSelectionRacing($selection->id) ) {
            throw new BetSelectionException($selection, "Invalid selction");
        }

        parent::validateSelection($selection);

    }
}