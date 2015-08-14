<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 9:40 AM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetAmountLimitExceededException;
use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetLimitExceededException;

class RacingBetTypeLimitValidator extends AbstractBetLimitValidator implements BetLimitValidator {

    protected $limitType = 'bet_type';

    /**
     * @inheritdoc
     */
    public function validateBet($betData)
    {
        //get bet limit
        $limit = $this->getBetLimitAmount($betData['user'], $betData['bet_type']);

        //get current bets on selections
        $currentBets = $this->betRepository->getBetsForSelectionsByBetType($betData['user'], $betData['selections'], $betData['bet_type']->id);

        $selections = array_pluck($betData['selections'], 'selection');

        //get the frequency of selection, used to calculate the total amount being bet on a selection
        //(stops placing multiple small bets on same selection at once)
        $frequency = array_count_values($selections);

        foreach (array_unique($selections) as $selection) {
            //get the total amount bet on a selection + the amount to be bet
            $amount = $frequency[$selection] * $betData['amount'] + $currentBets->filter(function($v) use ($selection) {
                    return $v->selection_id == $selection;
                })->sum(function ($v) { return $v->bet_amount; });

            //check the limit
            if ($amount > $limit) {
                throw new BetAmountLimitExceededException($limit);
            }
        }
    }
}