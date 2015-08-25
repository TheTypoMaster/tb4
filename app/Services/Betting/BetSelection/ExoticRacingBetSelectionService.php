<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 10:17 AM
 */

namespace TopBetta\Services\Betting\BetSelection;

use Lang;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class ExoticRacingBetSelectionService extends RacingBetSelectionService {

    /**
     * Overridden to cater for different payload structure for exotics
     * @inheritdoc
     */
    public function getAndValidateSelections($selections)
    {
        $selectionModels = array();
        $uniqueSelections = array();

        foreach($selections as $selection) {

            //if we've already retrieved the selection don't retrieve it again
            if( ! $selectionModel = array_get($uniqueSelections, $selection['id'], null) ) {
                //get the selection
                $selectionModel = $this->selectionService->getSelection($selection['id']);

                if( ! $selectionModel ) {
                    throw new BetSelectionException(null, "Selection not found");
                }

                //validate
                $this->validateSelection($selectionModel);

                //store
                $uniqueSelections[$selection['id']] = $selectionModel;
            }

            $selectionModels[] = array("selection" => $selectionModel, "position" => array_get($selection, "position", 0));

        }

        //make sure selections all belong to the same event
        if( ! $this->selectionService->selectionsBelongToSameEvent($uniqueSelections) ) {
            throw new BetSelectionException(null, "Selection not found in event");
        }

        return $selectionModels;
    }

    /**
     * @inheritdoc
     */
    public function createSelection($bet, $selection, $extraData = array())
    {
        return parent::createSelection($bet, $selection, array("position" => array_get($extraData, "position", 0)));
    }

    /**
     * Formats bet selections into format that exotic bet libraries expect
     * @param $selections
     * @return array
     * @throws BetSelectionException
     */
    public function formatSelectionsForExoticLibrary($selections)
    {
        //array maps
        $positionMap = array(1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth');
        $selectionsArray = array('first' => array(), 'second' => array(), 'third' => array(), 'fourth' => array());

        //if exotic bet is boxed only store first selections
        if( array_get($selections[0], 'position', 0) == 0 ) {
            $positionMap = array(0 => 'first');
            $selectionsArray = array('first' => array());
        }

        foreach($selections as $selection) {
            //invalid position
            if( ! $index = array_get($positionMap, array_get($selection, 'position', 0), null) ) {
                throw new BetSelectionException($selection['selection'], "Invalid position");
            }

            //store selection id
            $selectionsArray[$index][] = $selection['selection']->id;
        }

        return $selectionsArray;
    }

    /**
     * Creates the selection string
     * @param $selections
     * @return string
     */
    public function getSelectionString($selections)
    {
        $positions = array();

        //create position array
        foreach($selections as $selection) {
            if( ! isset($positions[array_get($selection, 'position', 0)]) ) {
                $positions[array_get($selection, 'position', 0)] = array();
            }

            //store the selection number
            $positions[array_get($selection, 'position', 0)][] = $selection['selection']->number;
        }

        //boxed selection
        if( count(array_get($positions, 0, null)) ) {
            return implode(', ', $positions[0]) . ' (BOXED)';
        }

        //create the selection string
        return implode(' / ', array_map(function ($v) {
            return implode(', ', $v);
        }, $positions));
    }
}