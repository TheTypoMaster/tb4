<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/09/2015
 * Time: 5:54 PM
 */

namespace TopBetta\Jobs\Pusher\Tournaments;


use TopBetta\Jobs\Pusher\PusherJob;

class TournamentSocketUpdate extends PusherJob{

    const CHANNEL_PREFIX = 'tournamentt_';

    public $queue = 'tournament-socket';

    public function __construct($data)
    {
        $this->channel = self::CHANNEL_PREFIX . $data['id'];
        parent::__construct($data);
    }
}