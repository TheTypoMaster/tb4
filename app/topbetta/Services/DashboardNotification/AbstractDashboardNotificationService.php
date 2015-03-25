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

    protected $queueService = 'Topbetta\Services\DashboardNotification\DashboardNotificationQueueService';

    public function notify($data)
    {
        //get the formatted payload
        $payload = $this->formatPayload($data);

        //set up the parameters
        $parameters = array(
            "http_method"  => $this->getHttpMethod(),
            "api_endpoint" => Config::get('dashboard.base_api_endpoint') . $this->getEndpoint(),
            "api_user"     => Config::get('dashboard.api_user'),
            "api_password" => Config::get('dashboard.api_password'),
        );

        //push job on to the queue
        Queue::push($this->queueService, array("payload" => $payload, "parameters" => $parameters), Config::get('dashboard.queue'));
    }

    abstract function getEndpoint();

    abstract function getHttpMethod();

    abstract function formatPayload($data);

}