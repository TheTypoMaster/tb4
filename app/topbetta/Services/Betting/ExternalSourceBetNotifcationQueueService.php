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
use Queue;
use Carbon;
use Config;

use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Services\Notifications\EmailNotificationService;
use TopBetta\Services\Helpers\FormatDataHelperService;

/**
 * Manages queuing bet notifications for external sources/API's
 *
 * Class ExternalSourceBetNotifcationQueueService
 * @package TopBetta\Services\Betting
 */
class ExternalSourceBetNotifcationQueueService {

    protected $emailnotification;
    protected $helper;
    protected $source;

    function __construct(BetSourceRepositoryInterface $source,
                         EmailNotificationService $emailnotification,
                         FormatDataHelperService $helper)
    {
        $this->emailnotification = $emailnotification;
        $this->helper = $helper;
        $this->source = $source;
    }

    /**
     * This function is called when the queue job is run
     *
     * @param $job
     * @param $data
     * @return bool
     */
    public function fire($job, $data){

        Log::debug('Bet Notification Queue Job Processing');

        $parameterData = $data['parameters'];
        $betData = $data['bet_data'];

        //file_put_contents('/tmp/pc-bet', json_encode($betData));
        // create guzzle client
        $client = new Client();

        // build up the request
        $request = $client->createRequest($parameterData['request_type'], $parameterData['source_details']['api_endpoint'],
                                                array('json' => $betData),
                                                array('auth' => array($parameterData['source_details']['api_username'],
                                                                        $parameterData['source_details']['api_password'])));

        // send bet details to bet source API
        try{
            $response = $client->send($request);
        }

        // connection problems
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

    /**
     * This method is called if the queue job fails. It is called by the Queue::failing event and is defined
     * in global.php
     *
     * @param $data
     */
    public function failed($data){

        $parameterData = $data['parameters'];
        $betData = $data['bet_data'];

        if(isset($parameterData['notification'])){
            if($parameterData['notification'] == 'email')
                $prettyJson = $this->helper->prettyJsonData(json_encode($data['bet_data']));

                $toEmailAddress = 'oliver@topbetta.com';
                $toName = 'Oliver Shanahan';
                $fromEmailAddress = 'notifications@topbetta.com';
                $fromName = 'TopBetta Server Notifications';
                $subject = 'Bet Notification Error: Counld not push to: '.$parameterData['source_details']['api_endpoint'];
                $body = "Error: Queue Push Error</br>
                         Type: Bet Notification</br>
                         Date/Time: ".Carbon\Carbon::now()."</br>
                         Enpoint: {$parameterData['source_details']['api_endpoint']}</br>
                         Payload: {$prettyJson}";

                $email = $this->emailnotification->notifyByEmail($toEmailAddress, $toName, $fromEmailAddress, $fromName, $subject, $body);
        }
    }


}