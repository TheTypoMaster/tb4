<?php

namespace TopBetta\Jobs\Pusher;

use Pusher;
use TopBetta\Jobs\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class PusherJob extends Command implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var
     */
    protected $data;

    protected $channel;

    protected $event = 'update';


    /**
     * Create a new command instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the command.
     *
     * @param Pusher $pusher
     */
    public function handle(Pusher $pusher)
    {
        $pusher->trigger($this->channel, $this->event, $this->data);
    }
}
