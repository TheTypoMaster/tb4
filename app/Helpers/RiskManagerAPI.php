<?php namespace TopBetta\Helpers;

use TopBetta\Models\BetResultStatus;
use Config;

class RiskManagerAPI
{

    public static function sendBetResult($betResultData)
    {
        // we only want to send to risk manager for production
         if (app()->environment() != Config::get('riskmanager.productionHost')) {
            return false;
        }

        $bet = array(
            'ReferenceId' => $betResultData->id,
            'Status' => BetResultStatus::where('id', $betResultData->bet_result_status_id)->value('name'),
            'Amount' => $betResultData->resultAmount,
            'ResultDate' => \Carbon\Carbon::now()
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
        // we only want to send to risk manager for production
        if (app()->environment() != Config::get('riskmanager.productionHost')) {
            return false;
        }

        // send bet to risk manager
        $responseJSON = CurlRequestHelper::curlRequest(Config::get('riskmanager.RISK_FEED_API'),
                                            Config::get('riskmanager.RISK_RACE_DATA_URI'),
                                            'POST',
                                            json_encode($raceData));

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

    public function sendResultData($resultsData)
    {
        // we only want to send to risk manager for production
        if (app()->environment() != Config::get('riskmanager.productionHost')) {
            return false;
        }


    }

    public function sendFixedOddsData($fixedOddsData)
    {
        // we only want to send to risk manager for production
        if (app()->environment() != Config::get('riskmanager.productionHost')) {
            return false;
        }


    }

}
