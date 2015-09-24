<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/09/2015
 * Time: 3:49 PM
 */

namespace TopBetta\Jobs\Pusher\Tournaments;


use TopBetta\Jobs\Pusher\PusherJob;

class CommentSocketUpdate extends PusherJob
{
    const CHANNEL_PREFIX = 'tournament_comment_';

    public function __construct($data)
    {
        $this->channel = self::CHANNEL_PREFIX . $data['tournament_id'];
        parent::__construct($data);
    }
}