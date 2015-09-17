<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 4:29 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetLiability\Factories\BetLiabilityCalculatorFactory;
use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetAmountLimitExceededException;
use TopBetta\Services\Betting\BetLimitValidation\Exceptions\BetExposureLimitExceeedException;

class ExposureLimitValidator extends AbstractBetLimitValidator implements BetLimitValidator
{

    protected $limitType = 'exposure';

    /**
     * @inheritdoc
     */
    public function validateBet($betData)
    {
        $limit = $this->getBetLimitAmount($betData['user'], $betData['bet_type']);

        $liabilityCalculator = BetLiabilityCalculatorFactory::make($betData['bet_type']->name);

        $liabilities = $liabilityCalculator->calculateLiability($betData);

        foreach ($liabilities as $event => $liability) {
            if ($liability > $limit) {
                throw new BetExposureLimitExceeedException($limit);
            }
        }

    }
}

