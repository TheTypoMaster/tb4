<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 4:39 PM
 */

namespace TopBetta\Jobs\Pusher;


class BetSocketUpdate extends PusherJob {

    public $queue = 'user-socket';

    const CHANNEL_PREFIX = 'bet_';

    public function __construct($data)
    {
        $this->channel = self::CHANNEL_PREFIX . $data['user_id'];
        parent::__construct($data);
    }
}