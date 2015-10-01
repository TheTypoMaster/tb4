<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 2:21 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


use TopBetta\Jobs\Pusher\PusherJob;
use TopBetta\Resources\SmallMeetingResource;

class MeetingSocketUpdate extends RacingSocketUpdate {

    const CHANNEL_PREFIX = 'meeting_';

    public $queue = 'meeting-socket';

    public function __construct($data)
    {
        $this->channel = self::CHANNEL_PREFIX . $data['id'];
        parent::__construct($data);
    }

}