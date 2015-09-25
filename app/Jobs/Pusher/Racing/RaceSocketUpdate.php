<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 2:53 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;

use Log;
use TopBetta\Jobs\Pusher\PusherJob;

class RaceSocketUpdate extends RacingSocketUpdate {

    const CHANNEL_PREFIX = 'race_';

    public function __construct($data)
    {
        Log::debug('###'.print_r($data));
        $this->channel = self::CHANNEL_PREFIX . $data['id'];
        parent::__construct($data);
    }

}