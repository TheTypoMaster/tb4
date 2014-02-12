<?php

namespace TopBetta;

class RiskManagerAPI
{

    static $riskManagerAPI = 'http://risk.mugbookie.com/api/v1';
    static $productionHost = 'services.topbetta.com.au';

    public static function sendBetResult($betResultData)
    {
        // we only want to send to risk manager for production
        if ($_SERVER['HTTP_HOST'] != static::$productionHost) {
            return false;
        }

        $betResultJSON = json_encode($betResultData);

        // send bet result to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(static::$riskManagerAPI, 'bets/' . $betResultData['ReferenceId'], 'PUT', $betResultJSON);

        $response = json_decode($responseJSON);

        if (!$response) {
            return false;
        }

        if ($response->status == 200) {
            return true;
        } else {
            return false;
        }
    }

}
