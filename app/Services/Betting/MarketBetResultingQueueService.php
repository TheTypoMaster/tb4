<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/10/2015
 * Time: 12:18 PM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Services\Betting\BetResults\BetResultService;

class MarketBetResultingQueueService {

    /**
     * @var MarketRepositoryInterface
     */
    private $market;
    /**
     * @var BetResultService
     */
    private $betResultService;


    public function __construct(MarketRepositoryInterface $market, BetResultService $betResultService)
    {
        $this->market = $market;
        $this->betResultService = $betResultService;
    }

    public function fire($job, $data)
    {
        if( ! $marketId = array_get($data, 'market_id', null) ) {
            \Log::error("No market id specified");
            return false;
        }

        $this->betResultService->resultBetsForMarket($this->market->find($marketId));

        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("MARKET BET RESULTING FAILED " . print_r($data,true));
    }
}