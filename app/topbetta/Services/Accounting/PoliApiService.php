<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/02/2015
 * Time: 9:29 AM
 */

namespace TopBetta\Services\Accounting;

use Config;
use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PoliApiService {


    public function initiateTransaction($data, $poliTransactionId, $userId)
    {

        //Add Merchant data to payload for API
        $data['MerchantReference'] = "TB_" . $poliTransactionId . "_" . $userId;
        $data['MerchantData'] = $userId.";".$poliTransactionId;

        $data['NotificationUrl'] = route(Config::get("poli.nudgeUrl"));

        $data['Timeout'] = array_get($data, 'Timeout', Config::get("poli.timeOut"));

        $redirectUrl = route(Config::get("poli.redirectRoute"));
        $data['SuccessURL'] = $redirectUrl;
        $data['FailureURL'] = $redirectUrl;
        $data['CancellationURL'] = $redirectUrl;
        $data['MerchantHomePageURL'] = Config::get("poli.homePage");

        $data['Amount'] = (float) $data['Amount']/100;


        //Send the request to the Poli API
        $client = $this->createClient();

        try {
            $response = $client->post(Config::get("poli.apiEndPoints.initiateTransaction"), array("body" => $data));
        }

        catch (RequestException $e) {
            return $e->getResponse();
        }

        return $response;
    }

    public function getTransactionDetails($token) {

        $client = $this->createClient();

        //Request the transaction details
        try {
            $response = $client->get(Config::get("poli.apiEndPoints.getTransactionDetails"), array("query" => array("token" => $token)));
        } catch (RequestException $e) {

            return $e->getResponse();
        }

        return $response;
    }

    private function createClient()
    {
        return new Client(array(
            "defaults" => array(
                "auth"  => array(
                    Config::get("poli.merchantId"),
                    Config::get("poli.merchantPassword"),
                ),
            ),
        ));
    }

}