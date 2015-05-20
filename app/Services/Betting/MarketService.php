<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/05/2015
 * Time: 10:29 AM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;

class MarketService {

    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;
    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;

    public function __construct(MarketRepositoryInterface $marketRepository, SelectionRepositoryInterface $selectionRepository)
    {
        $this->marketRepository = $marketRepository;
        $this->selectionRepository = $selectionRepository;
    }

    /**
     * Gets the market by id from repository
     * @param $marketId
     * @return mixed
     */
    public function getMarket($marketId)
    {
        return $this->marketRepository->find($marketId);
    }

    /**
     * Checks if market is open for selection
     * @param $selection
     * @return mixed
     */
    public function isSelectionMarketAvailableForBetting($selection)
    {
        if( is_int($selection) ) {
            $selection = $this->selectionRepository->find($selection);
        }

        return $this->isMarketAvailableForBetting($selection->market);
    }

    /**
     * Checks if market is open
     * @param $market
     * @return bool
     */
    public function isMarketAvailableForBetting($market)
    {
        if( is_int($market) ) {
           $market = $this->getMarket($market);
        }

        if( $market && $market->display_flag ) {
            return true;
        }

        return false;
    }
}