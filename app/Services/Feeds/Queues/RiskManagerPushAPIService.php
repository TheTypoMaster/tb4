<?php namespace TopBetta\Services\Feeds\Queues;

/**
 * Coded by Oliver Shanahan
 * File creation date: 15/09/15
 * File creation time: 09:06
 * Project: tb4
 */

use Config;
use Log;


class RiskManagerPushAPIService
{
    public function pushDataToRiskManagerAPI($data)
    {

        // send bet to risk manager
        $responseJSON = \TopBetta\Helpers\CurlRequestHelper::curlRequest(Config::get('riskmanager.RISK_FEED_API'),
            Config::get('riskmanager.RISK_RACE_DATA_URI'),
            'POST',
            json_encode($data));

        $response = json_decode($responseJSON);

        if (!$response) {
            return false;
        }

        Log::debug('RiskManagerAPI (sendResultData): Response - '.print_r($response,true));
        if ($response->http_status_code == 200) {
            return true;
        } else {
            return false;
        }
    }
}