<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 3:31 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


use TopBetta\Jobs\Pusher\PusherJob;

abstract class RacingSocketUpdate extends PusherJob {

    protected $queue = 'racing-socket';
}