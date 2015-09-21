<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 3:27 PM
 */

namespace TopBetta\Jobs\Pusher;


class UserSocketUpdate extends PusherJob {

    public $queue = 'user-socket';

    const CHANNEL_PREFIX = 'user_';

    public function __construct($data)
    {
        $this->channel = 'user_' . $data['id'];
        parent::__construct($data);
    }


}