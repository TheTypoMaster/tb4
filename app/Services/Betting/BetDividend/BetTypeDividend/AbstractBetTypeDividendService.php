<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:40 PM
 */

namespace TopBetta\Services\Betting\BetDividend\BetTypeDividend;


use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;
use TopBetta\Services\Betting\SelectionService;

abstract class AbstractBetTypeDividendService {

    /**
     * @var SelectionService
     */
    protected $selectionService;
    /**
     * @var ResultPricesRepositoryInterface
     */
    protected $resultPricesRepository;
    /**
     * @var SelectionResultRepositoryInterface
     */
    protected $resultRepository;

    public function __construct(SelectionService $selectionService, ResultPricesRepositoryInterface $resultPricesRepository, SelectionResultRepositoryInterface $resultRepository)
    {
        $this->selectionService = $selectionService;
        $this->resultPricesRepository = $resultPricesRepository;
        $this->resultRepository = $resultRepository;
    }

    /**
     * Gets the dividend for a resulted bet
     * @param $bet
     * @return float
     */
    abstract public function getResultedDividendForBet($bet);
}