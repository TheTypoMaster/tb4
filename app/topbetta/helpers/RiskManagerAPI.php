<?php

namespace TopBetta;

use Illuminate\Support\Facades\Config;

class RiskManagerAPI
{

    public static function sendBetResult($betResultData)
    {
        // we only want to send to risk manager for production
         if (app()->environment() != Config::get('riskmanager.productionHost')) {
            //return false;
        }

        $bet = array(
            'ReferenceId' => $betResultData->id,
            'Status' => BetResultStatus::where('id', $betResultData->bet_result_status_id)->pluck('name'),
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

}
