<?php

namespace TopBetta\Repositories;

use TopBetta\AccountBalance;
use TopBetta\Bet;
use TopBetta\BetResultStatus;
use TopBetta\FreeCreditBalance;
use TopBetta\RaceResult;

/**
 * Description of BetRepo
 *
 * @author mic
 */
class BetRepo
{

    /**
     * Lookup results for the selections made for this bet
     * Return the payout amount in cents if a winning bet
     * 
     * @param Bet $bet
     * @return int (cents)
     */
    public function getBetPayoutAmount(Bet $bet)
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
    public function payoutBet(Bet $bet, $amount)
    {
        // Free credit bets, we keep the original stake
        if ($bet->bet_freebet_flag == 1) {
            $amount -= $bet->bet_freebet_amount;
        }
        $bet->result_transaction_id = $this->awardBetWin($bet->user_id, $amount);
        $bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_PAID);
        $bet->resulted_flag = 1;

        return $bet->save();
    }

    /**
     * Refund a bet
     * 
     * @param \TopBetta\Bet $bet
     * @return Boolean
     */
    public function refundBet(Bet $bet)
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

        $bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_FULL_REFUND);
        $bet->refunded_flag = 1;
        $bet->resulted_flag = 1;

        return $bet->save();
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
