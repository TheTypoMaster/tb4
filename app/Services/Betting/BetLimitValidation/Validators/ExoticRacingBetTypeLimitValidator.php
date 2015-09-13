<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 12:17 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetAmountLimitExceededException;
use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetLimitExceededException;

class ExoticRacingBetTypeLimitValidator extends ExoticRacingBetLimitValidator implements BetLimitValidator {

    protected $limitType = 'bet_type';

    /**
     * @inheritdoc
     */
    public function validateBet($betData)
    {
        $limit = $this->getBetLimitAmount($betData['user'], $betData['bet_type']);

        $bets = $this->getBetsWithMatchingSelection($betData);

        //check we don't exceed bet limit
        if ($bets->sum('amount') + $betData['amount'] > $limit) {
            throw new BetAmountLimitExceededException($limit);
        }
    }
}