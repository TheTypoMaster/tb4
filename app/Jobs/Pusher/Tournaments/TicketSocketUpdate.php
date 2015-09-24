<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/09/2015
 * Time: 3:53 PM
 */

namespace TopBetta\Jobs\Pusher\Tournaments;


use TopBetta\Jobs\Pusher\PusherJob;

class TicketSocketUpdate extends PusherJob {

    const CHANNEL_PREFIX = 'tournament_ticket_';

    public function __construct($data)
    {
        $this->channel = self::CHANNEL_PREFIX . $data['user_id'];
        parent::__construct($data);
    }

}