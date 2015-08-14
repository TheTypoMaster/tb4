<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 11:45 AM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Validators;


use TopBetta\Repositories\Contracts\BetLimitTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;

abstract class AbstractBetLimitValidator implements BetLimitValidator {

    /**
     * Should be overriden in inheriting class
     * @var String
     */
    protected $limitType;

    /**
     * @var BetLimitTypeRepositoryInterface
     */
    protected $betLimitTypeRepository;

    /**
     * @var BetRepositoryInterface
     */
    protected $betRepository;

    public function __construct(BetLimitTypeRepositoryInterface $betLimitTypeRepository, BetRepositoryInterface $betRepository)
    {
        $this->betLimitTypeRepository = $betLimitTypeRepository;
        $this->betRepository = $betRepository;
    }

    public function getBetLimitAmount($user, $betType)
    {
        $betLimit = $this->betLimitTypeRepository->getLimitForUser($user, $betType->id, $this->limitType);

        return $betLimit->amount ? : $betLimit->default_amount;
    }
}