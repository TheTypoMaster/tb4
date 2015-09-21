<?php

namespace TopBetta\Console\Commands\Pusher;

use Pusher;
use Illuminate\Console\Command;

class PusherHeartbeat extends Command
{
    const PUSHER_HEARTBEAT_CHANNEL = 'heartbeat';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'topbetta:pusher-heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs the heartbeat for pusher.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Pusher $pusher)
    {
        $pusher->trigger(self::PUSHER_HEARTBEAT_CHANNEL, self::PUSHER_HEARTBEAT_CHANNEL, array(
            self::PUSHER_HEARTBEAT_CHANNEL => true
        ));
    }
}
