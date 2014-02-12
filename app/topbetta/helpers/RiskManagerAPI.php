<?php

namespace TopBetta;

use Illuminate\Support\Facades\Config;

class RiskManagerAPI
{

    public static function sendBetResult($betResultData)
    {
        // we only want to send to risk manager for production
        if ($_SERVER['HTTP_HOST'] != Config::get('riskmanager.productionHost')) {
            return false;
        }

        $betResultJSON = json_encode($betResultData);

        // send bet result to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.riskManagerAPI'), 'bets/' . $betResultData['ReferenceId'], 'PUT', $betResultJSON);

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
