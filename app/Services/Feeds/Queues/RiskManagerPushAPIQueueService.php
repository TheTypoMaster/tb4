<?php namespace TopBetta\Services\Feeds\Queues;

/**
 * Coded by Oliver Shanahan
 * File creation date: 8/09/15
 * File creation time: 09:05
 * Project: serena
 */

use Queue;
use Log;
use Config;

use TopBetta\Services\Feeds\Queues\RiskManagerPushAPIService;

class RiskManagerPushAPIQueueService
{
    protected $riskApiPush;

    public function __construct(RiskManagerPushAPIService $riskApiPush)
    {
        $this->riskApiPush = $riskApiPush;
    }

    public function fire($job, $data)
    {
        //if (Config::get('app.debug')) Log::debug('TopBettaAPIService: TopBetta API Push - In Job Service ', $data);
        $this->riskApiPush->pushDataToRiskManagerAPI($data);
        $job->delete();
    }
}