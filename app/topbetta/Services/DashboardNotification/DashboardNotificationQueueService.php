<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Services\DashboardNotification;

use Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DashboardNotificationQueueService {

    public function fire($job, $data)
    {
        //get the payload and parameters
        $payload = $data['payload'];

        $parameters = $data['parameters'];

        //create the request
        $client = new Client();

        //build the request payload
        $requestPayload = array('json' => $payload);

        if(array_get($parameters, 'api_user', false)) {
            $requestPayload['auth'] = array(
                $parameters['api_user'],
                $parameters['api_password'],
            );
        }

        $request = $client->createRequest($parameters['http_method'], $parameters['api_endpoint'], $requestPayload);

        //send the request
        try {
            $response = $client->send($request);
        } catch (RequestException $e) {
            Log::error("DashboardNotificationQueueService Guzzle Request Exception : " . $e->getMessage() . " Response " . $e->getResponse());
            return false;
        }

        if( $response->json()['http_status_code'] != 200 ) {
            Log::error("Dashboard API error : " . print_r($response->json(), true));
        }

        $job->delete();
    }

    public function failed($data)
    {
        Log::error("Added to failed job list : " . print_r($data, true));
    }

}