<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 2:18 PM
 */

namespace TopBetta\Services\Tournaments\Betting\Exceptions;


class TournamentBetLimitExceededException extends \Exception {

    public function __construct($event, $betLimit)
    {
        $message = "Tournament bet limit of $" . number_format($betLimit/100, 2) . " exceeed on event " . $event->name;
        parent::__construct($message);
    }
}