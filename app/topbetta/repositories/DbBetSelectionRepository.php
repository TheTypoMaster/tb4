<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/02/2015
 * Time: 10:09 AM
 */

namespace TopBetta\Repositories;


use TopBetta\BetSelection;
use TopBetta\Repositories\Contracts\BetSelectionRepositoryInterface;

class DbBetSelectionRepository implements BetSelectionRepositoryInterface
{

    private static $selectionPositionTemplate = array(
        "first"     => array(),
        "second"    => array(),
        "third"     => array(),
        "fourth"    => array()
    );

    private static $selectionBoxedTemplate = array(
        "first"     => array()
    );

    /**
     * Static function for retrieving an array of selections for an exotic bet
     * This is static for legacy reasons. Should be abstracted to a service.
     * @param $betId
     * @return array
     */
    public static function getUnscratchedExoticSelectionsInPositionForBet($betId, $boxed = false)
    {
        $betSelectionsArray = $boxed ? self::$selectionBoxedTemplate : self::$selectionPositionTemplate;

        //get bet selections which are not scratched.
        $betSelections = BetSelection::where("bet_id", "=", $betId)
            -> whereHas("selection", function($q) {
                $q->where("selection_status_id", "=", 1);
            })
            ->get();

        foreach($betSelections as $betSelection) {
            switch($betSelection->position) {
                case 0:
                case 1:
                    $betSelectionsArray['first'][] = $betSelection->selection_id;
                    break;
                case 2:
                    $betSelectionsArray['second'][] = $betSelection->selection_id;
                    break;
                case 3:
                    $betSelectionsArray['third'][] = $betSelection->selection_id;
                    break;
                case 4:
                   $betSelectionsArray['fourth'][] = $betSelection->selection_id;
                    break;
            }
        }

        return $betSelectionsArray;
    }

}