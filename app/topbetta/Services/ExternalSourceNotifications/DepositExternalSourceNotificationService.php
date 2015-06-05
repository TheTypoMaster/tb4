<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 3:50 PM
 */

namespace TopBetta\Services\ExternalSourceNotifications;


class DepositExternalSourceNotificationService extends AbstractExternalSourceNotificationService {

    protected $queueService = 'TopBetta\Services\ExternalSourceNotifications\Queue\DepositExternalSourceNotificationQueueService';

    protected $endpointKey = 'deposit_endpoint';

    protected $httpMethod = 'POST';
}