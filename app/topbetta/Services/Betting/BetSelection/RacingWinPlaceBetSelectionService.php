<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 2:33 PM
 */

namespace TopBetta\Services\Betting\BetSelection;


use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class RacingWinPlaceBetSelectionService extends AbstractBetSelectionService {

    public function getAndValidateSelections($selections)
    {
        $selectionModels = array();

        foreach($selections as $selection) {

            $selectionModel = $this->selectionService->getSelection($selection['id']);

            $this->validateSelection($selectionModel);

            $selectionModels[] = $selectionModel;
        }

        return $selectionModels;
    }

    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        foreach($selections as $selection) {
            $exceedLimit = $this->betLimitRepo->checkExceedBetLimitForBetData(array(
                'id'          => $selection->market->event->id,
                'selection'   => $selection->id,
                'bet_type_id' => $betType,
                'value'       => $amount,
            ), 'racing');

            if( $exceedLimit['result'] ) {
                return false;
            }
        }

        return true;
    }

    public function validateSelection($selection)
    {
        //TODO: MESSAGE!
        if( ! $this->selectionService->isSelectionRacing($selection->id) ) {
            throw new BetSelectionException($selection, "");
        }

        parent::validateSelection($selection);

    }
}