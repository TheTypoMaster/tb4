<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 11:46 AM
 */

namespace TopBetta\Services\DashboardNotification;

use Config;
use Queue;
use Log;
use TopBetta\Services\Notifications\EmailNotificationService;

abstract class AbstractDashboardNotificationService {

    protected $queueService;
    /**
     * @var
     */
    private $emailNotificationService;

    public function __construct(EmailNotificationService $emailNotificationService)
    {
        $this->emailNotificationService = $emailNotificationService;
    }

    /**
     * Puts a job on the queue to send a notification to the dashboard based on implementation
     * @param $data
     */
    public function notify($data)
    {
        try {
            //push job on to the queue
            Queue::push($this->queueService, array("payload" => $data), Config::get('dashboard.queue'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->sendErrorAlert($data, $e);
        }
    }

    public function sendErrorAlert($data, $error)
    {
        $subject = Config::get('dashboard.queue') . ' queue error in ' . $this->queueService;

        $body = 'Queue Service: ' . $this->queueService . '<br/>' .
            'Payload: ' . print_r($data,true) . '<br/>' .
            'Error: ' . $error->getMessage();

        //get to and from
        $to = Config::get('dashboard.error_email_to');
        $from = Config::get('dashboard.error_email_from');

        //send email
        $this->emailNotificationService->notifyByEmail($to['address'], $to['name'], $from['address'], $from['name'], $subject, $body);
    }

}