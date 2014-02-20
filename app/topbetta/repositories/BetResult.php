<?php

namespace TopBetta\Repositories;

use TopBetta\AccountBalance;
use TopBetta\Bet;
use TopBetta\BetResultStatus;
use TopBetta\FreeCreditBalance;
use TopBetta\RaceEvent;
use TopBetta\RaceResult;

/**
 * Description of BetResult
 *
 * @author mic
 */
class BetResult
{

    /**
     * Our race results for this event if any
     * 
     * @var array 
     */
    private $raceResults;

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
                ->with('selections', 'betType')
                ->get();
        $result = [];
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
        $this->raceResults = $resultModel->getResultsForRaceId($bet->event_id);

        if (!$this->raceResults) {
            return false;
        }

        $eventStatus = RaceEvent::where('id', $bet->event_id)->pluck('event_status_id');

        // RACE ABANDONED - REFUND BET
        if ($eventStatus == 3) {
            $bet = $this->refundBet($bet);

            return $bet->save();
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

        $payout = $this->getBetPayoutAmount($bet);
        if ($payout) {
            // WINNING BET
            $bet = $this->payoutBet($bet, $payout);
        }

        return $bet->save();
    }

    /**
     * Lookup results for the selections made for this bet
     * Return the payout amount in cents if a winning bet
     * 
     * @param Bet $bet
     * @return int (cents)
     */
    private function getBetPayoutAmount(Bet $bet)
    {
        $payout = 0;
        $dividend = 0;

        // TODO: maybe swap to using $this->raceResults for dividend
        switch ($bet->betType->name) {
            case 'win':
                $dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
                        ->where('win_dividend', '>', 0)
                        ->pluck('win_dividend');

                break;

            case 'place':
                $dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
                        ->where('place_dividend', '>', 0)
                        ->pluck('place_dividend');
                break;

            // TODO: EXOTICS
            // NEED TO LOOP OVER EACH SELECTION AND CHECK POSITION

            default:
                break;
        }

        // CALC THE RETURN AMOUNT IF IT WAS A WINNING BET
        if ($dividend > 0) {
            $payout = $bet->bet_amount * round($dividend, 2);
        }


        return (int) $payout;
    }

    /**
     * Awards user cash for bet win
     * 
     * @param Bet $bet
     * @param type $amount
     * @return Bet
     */
    private function payoutBet(Bet $bet, $amount)
    {
        // Free credit bets, we keep the original stake
        if ($bet->bet_freebet_flag == 1) {
            $amount -= $bet->bet_freebet_amount;
        }
        $bet->result_transaction_id = $this->awardBetWin($bet->user_id, $amount);
        $bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_PAID);
        $bet->resulted_flag = 1;

        return $bet;
    }

    /**
     * Refund a bet
     * 
     * @param \TopBetta\Bet $bet
     * @return \TopBetta\Bet
     */
    private function refundBet(Bet $bet)
    {
        // Full bet amount was on free credit
        if ($bet->bet_freebet_flag == 1 && $bet->bet_freebet_amount == $bet->bet_amount) {
            $bet->refund_freebet_transaction_id = $this->awardFreeBetRefund($bet->user_id, $bet->bet_freebet_amount);
        } else if ($bet->bet_freebet_flag == 1 && $bet->bet_freebet_amount < $bet->bet_amount) {
            // Free bet amount was less then refund
            $refundAmount = $bet->bet_amount - $bet->bet_freebet_amount;
            // Refund free bet amount
            $bet->refund_freebet_transaction_id = $this->awardFreeBetRefund($bet->user_id, $bet->bet_freebet_amount);
            // Refund balance to account
            $bet->refund_transaction_id = $this->awardBetRefund($bet->user_id, $refundAmount);
        } else {
            // No free credit was used - refund full amount to account
            $bet->refund_transaction_id = $this->awardBetRefund($bet->user_id, $bet->bet_amount);
        }

        $bet->bet_result_status_id = \TopBetta\BetResultStatus::getBetResultStatusByName(\TopBetta\BetResultStatus::STATUS_FULL_REFUND);
        $bet->refunded_flag = 1;
        $bet->resulted_flag = 1;

        return $bet;
    }

    /**
     * Increment a user's account balance
     *
     * @param object 	$user
     * @param integer 	$amount
     * @param string 	$keyword
     */
    private function awardCash($user_id, $amount, $keyword)
    {
        return AccountBalance::_increment($user_id, $amount, $keyword);
    }

    private function awardBetWin($user_id, $amount)
    {
        return $this->awardCash($user_id, $amount, AccountBalance::TYPE_BETWIN);
    }

    private function awardBetRefund($user_id, $amount)
    {
        return $this->awardCash($user_id, $amount, AccountBalance::TYPE_BETREFUND);
    }

    private function awardFreeBetRefund($user_id, $amount)
    {
        return FreeCreditBalance::_increment($user_id, $amount, FreeCreditBalance::TYPE_FREEBETREFUND);
    }

}
