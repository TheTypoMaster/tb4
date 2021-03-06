<?php namespace TopBetta\Helpers;

use TopBetta\Models\BetResultStatus;
use Config;
use Log;

class RiskManagerAPI
{

    public static function sendBetResult($betResultData, $amount = 0)
    {
        // we only want to send to risk manager for production
//         if (app()->environment() != Config::get('riskmanager.productionHost')) {
//            return false;
//        }

        $bet = array(
            'ReferenceId' => $betResultData->id,
            'Status' => BetResultStatus::where('id', $betResultData->bet_result_status_id)->value('name'),
            'Amount' => $amount,
            'ResultDate' => \Carbon\Carbon::now()->toDateTimeString(),
        );

        $betResultJSON = json_encode($bet);
		
		$endPoint = 'bets/';
		// sport bets are handled separately on risk
		if ($betResultData->bet_origin_id == 3) {
			$endPoint = 'sportbets/';
		}

        // send bet result to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.riskManagerAPI'), $endPoint . $bet['ReferenceId'], 'PUT', $betResultJSON);

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

    public static function sendRacingBet($betData)
    {
        return self::sendBet($betData, 'bets');
    }

    public static function sendSportsBet($betData)
    {
        return self::sendBet($betData, 'sportbets');
    }

    public static function sendBet($betData, $endPoint)
    {
        // we only want to send to risk manager for production
        if (app()->environment() != Config::get('riskmanager.productionHost')) {
            return false;
        }

        // send bet to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.riskManagerAPI'), $endPoint, 'POST', json_encode($betData));

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

    public function sendRaceStatus($raceData)
    {
        Log::debug('RiskManagerAPI (sendRaceStatus): Race Status Payload', $raceData);
        // we only want to send to risk manager for production
//        if (app()->environment() != Config::get('riskmanager.productionHost')) {
//            Log::debug('RiskManagerAPI (sendRaceStatus): App env not va', $raceData);
//            return false;
//        }

        // send bet to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.RISK_FEED_API'),
                                            Config::get('riskmanager.RISK_RACE_DATA_URI'),
                                            'POST',
                                            json_encode($raceData));

        $response = json_decode($responseJSON);

        if (!$response) {
            Log::debug('RiskManagerAPI (sendRaceStatus): No response from Risk API');
            return false;
        }

        Log::debug('RiskManagerAPI (sendRaceStatus): Response - '.print_r($response,true));
        if ($response->http_status_code == 200) {
            return true;
        } else {
            return false;
        }

    }

    public function sendResultData($resultsData)
    {

        // send bet to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.RISK_FEED_API'),
            Config::get('riskmanager.RISK_RACE_DATA_URI'),
            'POST',
            json_encode($resultsData));

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

    public function sendFixedOddsData($fixedOddsData)
    {
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.RISK_FEED_API'),
            Config::get('riskmanager.RISK_RACE_DATA_URI'),
            'POST',
            json_encode($fixedOddsData));

        $response = json_decode($responseJSON);

        if (!$response) {
            return false;
        }

        Log::debug('RiskManagerAPI (sendFixedOddsData): Response - '.print_r($response,true));
        if ($response->http_status_code == 200) {
            return true;
        } else {
            return false;
        }



    }

    public function sendRunnerScratchedStatus($runnerScratched)
    {
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.RISK_FEED_API'),
            Config::get('riskmanager.RISK_RACE_DATA_URI'),
            'POST',
            json_encode($runnerScratched));

        $response = json_decode($responseJSON);

        if (!$response) {
            return false;
        }

        Log::debug('RiskManagerAPI (sendRunnerScratchedStatus): Response - '.print_r($response,true));
        if ($response->http_status_code == 200) {
            return true;
        } else {
            return false;
        }



    }
}
