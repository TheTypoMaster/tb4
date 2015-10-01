<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 3:25 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


class NextToJumpSocketUpdate extends RacingSocketUpdate {

    protected $channel = 'racing_n2j';

    public $queue = 'racing-n2j';
}