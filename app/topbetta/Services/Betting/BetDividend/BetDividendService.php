<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 3:51 PM
 */

namespace TopBetta\Services\Betting\BetDividend;


use TopBetta\Services\Betting\BetDividend\BetTypeDividend\AbstractBetTypeDividendService;
use TopBetta\Services\Betting\Factories\BetTypeDividendServiceFactory;

class BetDividendService {

    /**
     * Store the dividend services in memory so we don't need to keep making them
     * @var array
     */
    private $betTypeDividendServices = array();

    /**
     * Gets the dividend for a resulted bet
     * @param $bet
     * @return float
     */
    public function getResultedDividendForBet($bet)
    {
        $betTypeDividendService = $this->getBetTypeDividendService($bet->type->name);

        return $betTypeDividendService->getResultedDividendForBet($bet);
    }

    /**
     * Gets the dividend service to use
     * @param $betType
     * @return AbstractBetTypeDividendService
     */
    public function getBetTypeDividendService($betType)
    {
        if ( ! array_Get($this->betTypeDividendServices, $betType, null) ) {
            $this->betTypeDividendServices[$betType] = BetTypeDividendServiceFactory::make($betType);
        }

        return $this->betTypeDividendServices[$betType];
    }
}