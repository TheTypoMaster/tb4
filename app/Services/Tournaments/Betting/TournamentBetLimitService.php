<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 1:04 PM
 */

namespace TopBetta\Services\Tournaments\Betting;


use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Services\Tournaments\Betting\Exceptions\TournamentBetLimitExceededException;

class TournamentBetLimitService {

    /**
     * @var TournamentBetRepositoryInterface
     */
    private $betRepository;


    public function __construct(TournamentBetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    public function checkSingeSelectionBetLimit($ticket, $selections, $amount)
    {
        $eventLimits = array();

        foreach($selections as $selection) {
            $event = $selection->market->event;

            if(! array_key_exists($event->id, $eventLimits) ) {
                $bets = $this->betRepository->getBetsOnEventForTicket($ticket->id, $event->id)->sum(function($v){ return $v->bet_amount; });
                $eventLimits[$event->id] = $bets;
            }

            $eventLimits[$event->id] += $amount;

            if( $ticket->tournament->bet_limit_per_event && $eventLimits[$event->id] > $ticket->tournament->bet_limit_per_event ) {
                throw new TournamentBetLimitExceededException($event, $ticket->tournament->bet_limit_per_event);
            }
        }
    }
}