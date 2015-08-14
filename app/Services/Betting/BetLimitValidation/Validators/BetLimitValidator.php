<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 9:36 AM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


interface BetLimitValidator {

    /**
     * Validates the bet data
     * @param array $betData -> ['user' => user, 'amount' => amount, 'bet_type' => bettype, 'selections' => [selections]]
     * @return void
     * @throws \TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetLimitExceededException
     */
    public function validateBet($betData);
}