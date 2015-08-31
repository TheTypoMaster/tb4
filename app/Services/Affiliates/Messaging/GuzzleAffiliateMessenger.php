<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/08/2015
 * Time: 9:41 AM
 */

namespace TopBetta\Services\Affiliates\Messaging;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use TopBetta\Services\Affiliates\Exceptions\AffiliateMessageException;
use TopBetta\Services\Affiliates\Exceptions\AffiliateResponseException;

class GuzzleAffiliateMessenger implements AffiliateMessenger {

    private $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function send($affiliateEndpoint, $affiliateMessage, $method = "POST")
    {
        $params = array(
            "auth" => array(
                $affiliateEndpoint->affiliate_endpoint_username,
                $affiliateEndpoint->affiliate_endpoint_password,
            )
        );

        $request = $this->client->createRequest($method, $affiliateEndpoint->affiliate_api_endpoint, array_merge($params, array("json" => $affiliateMessage->toArray())));

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            throw new AffiliateMessageException($affiliateMessage, $e->hasResponse() ? $e->getResponse()->json() : null, $e->getMessage());
        }

        if (! $response->json()) {
            throw new AffiliateResponseException($response, "Invalid response");
        }

        return $response->json();
    }
}