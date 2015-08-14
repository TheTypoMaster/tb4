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
     * @param \TopBetta\Models\BetModel $bet
     * @return void
     * @throws \TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetLimitExceededException
     */
    public function validateBet($bet);
}