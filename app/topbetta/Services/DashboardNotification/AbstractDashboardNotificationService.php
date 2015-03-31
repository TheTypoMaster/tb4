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

abstract class AbstractDashboardNotificationService {

    protected $queueService;

    /**
     * Puts a job on the queue to send a notification to the dashboard based on implementation
     * @param $data
     */
    public function notify($data)
    {
        //push job on to the queue
        Queue::push($this->queueService, array("payload" => $data), Config::get('dashboard.queue'));
    }

}