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
            Log::error("Dashboard Notification Error: Empty payload");
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

    protected function formatUser($user) {

        if( ! count($user) ) {
            return array();
        }

        //get the users name
        if( array_get($user, 'topbettauser', null) ) {
            $firstName = array_get($user, 'topbettauser.first_name', null);
            $lastName = array_get($user, 'topbettauser.last_name', null);;
        } else {
            $names = explode(' ', array_get($user, 'name', null));
            $firstName = array_get($names, 0, "");
            $lastName = array_get($names, 1, "");
        }

        //create payload
        return array(
            "external_id"           => array_get($user, 'id', null) ? 'tb_' . array_get($user, 'id', null) : null,
            "user_username"         => array_get($user, 'username', null),
            "user_first_name"       => $firstName,
            "user_last_name"        => $lastName,
            "user_email"            => array_get($user, 'email', null),
            "user_street"           => array_get($user, 'topbettauser.street', null),
            "user_city"             => array_get($user, 'topbettauser.city', null),
            "user_state"            => array_get($user, 'topbettauser.state', null),
            "user_postcode"         => array_get($user, 'topbettauser.postcode', null),
            "user_country"          => array_get($user, 'topbettauser.country', null),
            "user_dob"              => array_get($user, 'topbettauser.dob_year', null) . "-" . array_get($user, 'topbettauser.dob_month', null) . "-" . array_get($user, 'topbettauser.dob_day', null),
            "user_marketing_opt_in" => (bool) array_get($user, 'topbettauser.marketing_opt_in_flag', null),
            "user_source"           => array_get($user, 'topbettauser.source', null),
            "user_is_corporate"     => (bool) array_get($user, 'isCorporate', null),
            "user_is_topbetta"      => (bool) array_get($user, 'isTopBetta', null),
            "user_block"            => (bool) array_get($user, 'block', null),
            "user_activated"        => (bool) array_get($user, 'activated_flag'),
            "user_btag"             => array_get($user, 'topbettauser.btag', null),
            "user_register_date"=> array_get($user, 'registerDate', null),
        );
    }

    abstract public function formatPayload($data);
}