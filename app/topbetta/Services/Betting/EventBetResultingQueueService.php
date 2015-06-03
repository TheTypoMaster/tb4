<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/05/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting;

use Log;
use TopBetta\Repositories\BetResultRepo;

class EventBetResultingQueueService {

    /**
     * @var BetResultRepo
     */
    private $betResultRepo;

    public function __construct(BetResultRepo $betResultRepo)
    {
        $this->betResultRepo = $betResultRepo;
    }

    public function fire($job, $data)
    {
        if( ! $eventId = array_get($data, 'event_id', null) ) {
            Log::error("No event id specified");
            return false;
        }

        $result = $this->betResultRepo->resultAllBetsForEvent($eventId);

        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("BET RESULTING FAILED " . print_r($data,true));
    }
}