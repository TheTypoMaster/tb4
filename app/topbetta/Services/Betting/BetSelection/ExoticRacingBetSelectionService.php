<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 10:17 AM
 */

namespace TopBetta\Services\Betting\BetSelection;


use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class ExoticRacingBetSelectionService extends RacingBetSelectionService {

    public function getAndValidateSelections($selections)
    {
        $selectionModels = array();
        $uniqueSelections = array();

        foreach($selections as $position => $positionSelections) {

            $selectionModels[$position] = array();

            foreach ($positionSelections as $selection) {

                if( ! $selectionModel = array_get($uniqueSelections, $selection['id'], null) ) {
                    $selectionModel = $this->selectionService->getSelection($selection['id']);

                    $this->validateSelection($selectionModel);

                    $uniqueSelections[$selection['id']] = $selectionModel;
                }

                $selectionModels[$position][] = $selectionModel;
            }

            if( count(array_unique($selectionModels[$position])) != count($selectionModels[$position]) ) {
                throw new BetSelectionException(null, "Duplicated selections for position " . $position);
            }
        }

        return $selectionModels;
    }

    public function createSelections($bet, $selections)
    {
        $positionNo = count($selections) > 1 ? 1 : 0;
        $betSelections = array();

        foreach($selections as $position => $positionSelections) {
            foreach($positionSelections as $selection) {
               $betSelections[] = $this->createSelection($bet, $selection, array('position' => $positionNo));
            }

            $positionNo++;
        }

        return $betSelections;
    }

    public function getSelectionString($selections)
    {
        return implode(' / ', array_map( function($v) {
            return implode(', ', array_map(function($selection) {
                return $selection->number;
            }, $v));
        }, $selections));
    }
}