<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 4:29 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


class RacingExposureLimitValidator extends AbstractBetLimitValidator implements BetLimitValidator {

    protected $limitType = 'exposure_racing';

    /**
     * @inheritdoc
     */
    public function validateBet($betData)
    {

    }
}