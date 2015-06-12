<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Services\ExternalSourceNotifications\Queue;

use Log;
use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;

abstract class ExternalSourceNotificationQueueService {

    /**
     * @var BetSourceRepositoryInterface
     */
    private $betSourceRepository;

    public function __construct(BetSourceRepositoryInterface $betSourceRepository)
    {
        $this->betSourceRepository = $betSourceRepository;
    }

    public function fire($job, $data)
    {
        //get the payload and parameters
        $payload = $this->formatPayload($data['payload']);

        if( empty($payload) ) {
            Log::error("External Source Notification Error: Empty payload");
            return false;
        }

        $parameters = array_get($data, 'parameters', array());

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
            Log::error("ExternalSourceNotificationQueueService Guzzle Request Exception : " . $e->getMessage());
            return false;
        }

        if( $response->json()['http_status_code'] != 200 ) {
            Log::error("External Source API error : " . print_r($response->json(), true));
        }

        $job->delete();
    }

    public function failed($data)
    {
        Log::error("Added to failed job list : " . print_r($data, true));
    }

    abstract public function formatPayload($data);
}