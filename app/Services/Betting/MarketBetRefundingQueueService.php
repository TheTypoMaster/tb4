<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/06/2015
 * Time: 3:03 PM
 */

namespace TopBetta\Services\Betting;

use Log;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;

class MarketBetRefundingQueueService {

    /**
     * @var MarketRepositoryInterface
     */
    private $market;
    /**
     * @var TournamentBetResultService
     */
    private $tournamentBetResultService;

    public function __construct(MarketRepositoryInterface $market, TournamentBetResultService $tournamentBetResultService)
    {
        $this->market = $market;
        $this->tournamentBetResultService = $tournamentBetResultService;
    }

    public function fire($job, $data)
    {
        if( ! $marketId = array_get($data, 'market_id', null) ) {
            Log::error("No market id specified");
            return false;
        }

        $this->tournamentBetResultService->refundBetsForMarket($marketId);

        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("BET REFUNDING FAILED " . print_r($data,true));
    }
}