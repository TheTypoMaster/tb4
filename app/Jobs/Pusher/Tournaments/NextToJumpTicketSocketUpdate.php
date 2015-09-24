<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/09/2015
 * Time: 4:05 PM
 */

namespace TopBetta\Jobs\Pusher\Tournaments;


use TopBetta\Jobs\Pusher\PusherJob;

class NextToJumpTicketSocketUpdate extends PusherJob {

    const CHANNEL_PREFIX = 'tournament_ticket_n2j_';

    public function __construct($userId, $data)
    {
        $this->channel = self::CHANNEL_PREFIX . $userId;
        parent::__construct($data);
    }
}