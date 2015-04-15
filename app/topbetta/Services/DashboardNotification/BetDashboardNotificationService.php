<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/03/2015
 * Time: 4:15 PM
 */

namespace TopBetta\Services\DashboardNotification;

class BetDashboardNotificationService extends AbstractDashboardNotificationService {

   protected $queueService = 'TopBetta\Services\DashboardNotification\Queue\BetDashboardNotificationQueueService';

}