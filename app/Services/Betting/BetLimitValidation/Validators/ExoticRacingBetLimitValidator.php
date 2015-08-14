<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 12:30 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


abstract class ExoticRacingBetLimitValidator extends AbstractBetLimitValidator{

    public function getBetsWithMatchingSelection($betData)
    {
        //get bets by type
        $bets = $this->betRepository->getBetsByTypeForEvent($betData['user'], $betData['event'], $betData['bet_type']->id);

        //filter bets to get only those on the same selections. To do this we filter the bet selection records by
        //checking if they exists in $betData['selections'] and then checking the number of filtered records is the
        //same as the number of unfiltered records.
        $bets = $bets->filter(function($v) use ($betData) {
            return $v->betselection->filter(function ($s) use ($betData) {
                return in_array(array('selection' => $s->selection_id, 'position' => $s['position']), $betData['selections']);
            })->count() == $v->betselection->count();
        });

        return $bets;
    }
}