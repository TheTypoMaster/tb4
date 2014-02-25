<?php

namespace TopBetta\Repositories;

use TopBetta\Bet;
use TopBetta\BetResultStatus;
use TopBetta\Facades\BetRepo;
use TopBetta\RaceEvent;
use TopBetta\RaceResult;

/**
 * Description of BetResult
 *
 * @author mic
 */
class BetResultRepo
{

    /**
     * Find and result all events that have pending bets if the 
     * event is marked as paying.
     * 
     * This can be used as a watchdog to make sure all valid
     * bets get paid when a race is set to status paid
     * 
     * @return string
     */
    public function resultAllBetsForPayingEvents()
    {
        $events = Bet::where('bet_result_status_id', 1)
                ->where('resulted_flag', 0)
                ->where('tbdb_event.event_status_id', 2)
                ->join('tbdb_event', 'tbdb_bet.event_id', '=', 'tbdb_event.id')
                ->groupBy('tbdb_bet.event_id')
                ->select('tbdb_bet.event_id')
                ->get();

        $result = array();
        
        if (count($events)) {
            foreach ($events as $event) {
                echo "Resulting event: " . $event->event_id . " \n";
                $result[$event->event_id] = $this->resultAllBetsForEvent($event->event_id);
            }
        } else {
            $result[] = "No events to result";
        }
        
        echo "-------\n";

        return $result;
    }

    /**
     * Find and result all unresulted bets for an event
     * 
     * @param int $eventId
     * @return array
     */
    public function resultAllBetsForEvent($eventId)
    {
        // we only want bets that are "unresulted" status id: 1
        $bets = Bet::where('event_id', $eventId)
                ->where('bet_result_status_id', 1)
                ->where('resulted_flag', 0)
                ->with('selections')
                ->get();

        $result = array();

        foreach ($bets as $bet) {
            $result[$bet->id] = $this->resultBet($bet);
        }

        return $result;
    }

    /**
     * Result an individual bet object
     * 
     * @param Bet $bet
     * @return bool
     */
    public function resultBet(Bet $bet)
    {
        $processBet = false;

        $resultModel = new RaceResult;
        $raceResults = $resultModel->getResultsForRaceId($bet->event_id);

        // Sanity check - Make sure we at least have a win_dividend
        if (!isset($raceResults['positions'][1]['win_dividend'])) {
            return false;
        }

        $eventStatus = RaceEvent::where('id', $bet->event_id)->pluck('event_status_id');

        // RACE ABANDONED - REFUND BET
        if ($eventStatus == 3) {
            return BetRepo::refundBet($bet);
        }

        // RACE PAYING INTERIM/FINAL
        if ($eventStatus == 6 && $bet->betType->name == 'win') {
            // RULE 1: Status interim - Result all "Winning" bets for Win, leave the ones that didn't win in case there is a protest
            $processBet = true;
        } elseif ($eventStatus == 2) {
            // RULE 2: Status paying - Result all other bets at Final Dividends
            $processBet = true;
            $bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_PAID);
            $bet->resulted_flag = 1;
        }

        if (!$processBet) {
            return false;
        }

        $payout = BetRepo::getBetPayoutAmount($bet);
        if ($payout) {
            // WINNING BET
            return BetRepo::payoutBet($bet, $payout);
        }

        return $bet->save();
    }

}
