<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 2:56 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


use TopBetta\Jobs\Pusher\PusherJob;

class TodaysRacingSocketUpdate extends RacingSocketUpdate {

    const CHANNEL_SMALL_MEETINGS = 'meeting_races';

    public $queue = 'todays-racing-socket';

    public function __construct($data)
    {
        parent::__construct($data);

        $this->channel = self::CHANNEL_SMALL_MEETINGS;
    }

}