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
        //get the payload and the parameters
        $payload = $data['payload'];

        $parameters = $data['parameters'];

        //create the client
        $client = new Client();

        //create the request
        $request = $client->createRequest($parameters['http_method'], $parameters['api_endpoint'], array(
            "auth" => array(
                $parameters['api_user'],
                $parameters['api_password'],
            ),
            'json' => $payload,
        ));

        //send the request
        try {
            $client->send($request);
        } catch (RequestException $e) {
            Log::error("DashboardNotificationQueueService Guzzle Request Exception : " . $e->getMessage() . " Response " . $e->getResponse());
            return false;
        }

        $job->delete();
    }

    public function failed($data)
    {
        Log::error("Added to failed job list : " . print_r($data, true));
    }

}