<?php namespace TopBetta\Services\Betting;

/**
 * Coded by Oliver Shanahan
 * File creation date: 19/01/15
 * File creation time: 11:57
 * Project: tb4
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Log;
use Exception;

use TopBetta\Services\Notifications\EmailNotificationService;

class ExternalSourceBetNotifcationQueueService {

    protected $emailnotification;

    function __construct(EmailNotificationService $emailnotification)
    {
        $this->emailnotification = $emailnotification;
    }


    public function fire($job, $data){

        $parameterData = $data['parameters'];
        $betData = $data['bet_data'];

        // create guzzle client
        $client = new Client(['base_url' => $parameterData['api_endpoint']]);

        // build up the request
        $request = $client->post($parameterData['api_endpoint'], $betData);

//                                    ->setAuth(Config::get('dataProducts.topBettaApiUsername'),
//                                        Config::get('dataProducts.topBettaApiPassword'),
//                                        'Basic');

        // send bet details to bet source API
        try{
            $response = $request->send();
        }

        // network timeouts etc
        catch (RequestException $e)
        {
            Log::error(get_class($this).' - Guzzle Request Exception: '. $e->getMessage());
            return false;
        }

        // 400's
        catch (ClientException $e)
        {
            Log::error(get_class($this).' - Guzzle Client Exception: '. $e->getMessage());
            return false;
        }

        // 500's
        catch (ServerException $e)
        {
            Log::error(get_class($this).' - Guzzle Server Exception: '. $e->getMessage());
            return false;
        }

        $job->delete();
    }

}