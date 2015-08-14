<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 12:36 PM
 */

namespace TopBetta\Services\Betting\BetLimitValidation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use TopBetta\Models\BetTypeModel;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetLimitValidation\Validators\BetLimitValidator;

class BetLimitValidationService {

    /**
     * IoC container
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Creates validator stack and validates bet
     * @param $betData
     */
    public function validateBet($betData)
    {
        $stack = $this->createValidatorStack($betData['bet_type']);

        foreach ($stack as $validator) {
            $validator->validateBet($betData);
        }
    }

    /**
     * Create the validator stack based on bet type
     * @param BetTypeModel $betType
     * @return Collection
     */
    protected function createValidatorStack(BetTypeModel $betType)
    {
        $validatorStack = new Collection;

        if ($betType->isExotic()) {
            $validatorStack->push($this->container->make('TopBetta\Services\Betting\BetLimitValidation\Validators\ExoticRacingBetTypeLimitValidator'));
            $validatorStack->push($this->container->make('TopBetta\Services\Betting\BetLimitValidation\Validators\ExoticRacingFlexiLimitValidator'));
        } else if ($betType->name == BetTypeRepositoryInterface::TYPE_SPORT) {
            $validatorStack->push($this->container->make('TopBetta\Services\Betting\BetLimitValidation\Validators\SportsBetTypeLimitValidator'));
        } else {
            $validatorStack->push($this->container->make('TopBetta\Services\Betting\BetLimitValidation\Validators\RacingBetTypeLimitValidator'));
        }

        return $validatorStack;
    }

}