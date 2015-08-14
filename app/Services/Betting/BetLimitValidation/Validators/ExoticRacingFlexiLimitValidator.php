<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 12:28 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetLimitExceededException;

class ExoticRacingFlexiLimitValidator extends ExoticRacingBetLimitValidator implements BetLimitValidator {

    protected $limitType = 'bet_flexi';

    /**
     * @inheritdoc
     */
    public function validateBet($betData)
    {
        $limit = $this->getBetLimitAmount($betData['user'], $betData['bet_type']);

        $bets = $this->getBetsWithMatchingSelection($betData);

        if ($bets->sum('percentage') + $betData['percentage'] > $limit) {
            throw new BetLimitExceededException;
        }
    }
}