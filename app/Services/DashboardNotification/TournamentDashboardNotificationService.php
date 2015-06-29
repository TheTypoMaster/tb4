<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/03/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Services\DashboardNotification;


use TopBetta\Repositories\DbTournamentTicketRepository;

class TournamentDashboardNotificationService extends AbstractDashboardNotificationService {

   protected $queueService = 'TopBetta\Services\DashboardNotification\Queue\TournamentDashboardNotificationQueueService';

}