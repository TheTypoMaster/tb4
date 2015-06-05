<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 11:46 AM
 */

namespace TopBetta\Services\ExternalSourceNotifications;

use Config;
use Queue;
use Log;
use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Services\Notifications\EmailNotificationService;

abstract class AbstractExternalSourceNotificationService {

    protected $endpointKey;

    protected $httpMethod;

    protected $queueService;

    /**
     * @var
     */
    private $emailNotificationService;
    /**
     * @var BetSourceRepositoryInterface
     */
    private $betSourceRepository;

    public function __construct(EmailNotificationService $emailNotificationService, BetSourceRepositoryInterface $betSourceRepository)
    {
        $this->emailNotificationService = $emailNotificationService;
        $this->betSourceRepository = $betSourceRepository;
    }

    /**
     * Puts a job on the queue to send a notification to the dashboard based on implementation
     * @param $data
     */
    public function notify($data)
    {
        if( ! $parameters = $this->getParameters(array_get($data, 'source', null)) ) {
            return;
        }

        try {
            //push job on to the queue
            Queue::push($this->queueService, array("payload" => $data, "parameters" => $parameters), Config::get('externalsource.queue'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->sendErrorAlert($data, $e);
        }
    }

    public function sendErrorAlert($data, $error)
    {
        $subject = Config::get('dashboard.queue') . ' queue error in ' . $this->queueService;

        $body = 'Queue Service: ' . $this->queueService . '<br/>' .
            'Payload: ' . print_r($data, true) . '<br/>' .
            'Error: ' . $error->getMessage();

        //get to and from
        $to   = Config::get('dashboard.error_email_to');
        $from = Config::get('dashboard.error_email_from');

        //send email
        $this->emailNotificationService->notifyByEmail($to['address'], $to['name'], $from['address'], $from['name'], $subject, $body);
    }

    public function getParameters($source)
    {
        if( ! $source ) {
            return null;
        }

        $sourceModel = $this->betSourceRepository->getSourceByKeyword($source);

        if( ! $sourceModel ) {
            return null;
        }

        return array(
            "api_endpoint" => $sourceModel['api_endpoint'][$this->endpointKey],
            "api_user" => $sourceModel['api_username'],
            "api_password" => $sourceModel['api_password'],
            "http_method" => $this->httpMethod
        );
    }
}