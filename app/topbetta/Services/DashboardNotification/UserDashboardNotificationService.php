<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/03/2015
 * Time: 9:45 AM
 */

namespace TopBetta\Services\DashboardNotification;


class UserDashboardNotificationService extends AbstractDashboardNotificationService {

    protected $queueService = 'TopBetta\Services\DashboardNotification\Queue\UserDashboardNotificationQueueService';
}