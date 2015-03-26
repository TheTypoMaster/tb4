<?php

namespace TopBetta\Repositories;

use TopBetta\AccountBalance;
use TopBetta\Bet;
use TopBetta\BetResultStatus;
use TopBetta\BetSelection;
use TopBetta\FreeCreditBalance;
use TopBetta\RaceEvent;
use TopBetta\RaceResult;
use TopBetta\Services\DashboardNotification\BetDashboardNotificationService;
use TopBetta\SportsSelectionResults;
use Carbon;

/**
 * Description of BetRepo
 *
 * @author mic
 */
class BetRepo
{

    /**
     * @var BetDashboardNotificationService
     */
    private $betDashboardNotificationService;

    public function __construct(BetDashboardNotificationService $betDashboardNotificationService)
    {
        $this->betDashboardNotificationService = $betDashboardNotificationService;
    }

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

		switch ($bet->betType->name) {
			case 'win':
				if ($bet->bet_origin_id == 2) {
					// RACING: simple check - do we have a result record for this selection id and win dividend
					$dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
							->where('win_dividend', '>', 0)
							->pluck('win_dividend');
				} elseif ($bet->bet_origin_id == 3) {
					// SPORTS: check for a result record
					$win = SportsSelectionResults::selectionResultExists($bet->selections[0]->selection_id);

					// Get fixed odd if it's a win
					if ($win) {
						$dividend = $this->getFixedOddsForSportsBet($bet);
					}
				}
				break;

			case 'place':
				// simple check: do we have a result record for this selection id and place dividend
				$dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
						->where('place_dividend', '>', 0)
						->pluck('place_dividend');
				break;

			// EXOTICS
			case 'quinella':
				if ($this->checkWinningExoticbet($bet)) {
					$payout = $this->getExoticDividendForBet($bet, 'quinella') * 100;
				}
				break;

			case 'exacta':
				if ($this->checkWinningExoticbet($bet)) {
					$payout = $this->getExoticDividendForBet($bet, 'exacta') * 100;
				}
				break;

			case 'trifecta':
				if ($this->checkWinningExoticbet($bet)) {
					$payout = $this->getExoticDividendForBet($bet, 'trifecta') * 100;
				}
				break;

			case 'firstfour':
				if ($this->checkWinningExoticbet($bet)) {
					$payout = $this->getExoticDividendForBet($bet, 'firstfour') * 100;
				}
				break;

			default:
				break;
		}

		// CALC THE RETURN AMOUNT IF IT WAS A WINNING BET
		if ($dividend > 0) {
			//$payout = $bet->bet_amount * round($dividend, 2);
			$payout = bcmul($bet->bet_amount, round($dividend, 2));
		}

		return (int) $payout;
	}

	public function checkWinningExoticbet(Bet $bet)
	{
		$exoticsMinWin = array(
			'quinella' => 2,
			'exacta' => 2,
			'trifecta' => 3,
			'firstfour' => 4
		);

		$winCount = 0;

		// loop over selections and find a matching record in the results table
		foreach ($bet->selections as $selection) {
			$position = RaceResult::where('selection_id', $selection->selection_id)
					->where('position', '>', 0)
					->pluck('position');

			switch ($bet->betType->name) {
				case 'quinella':
					// 2 selections any order in 1st or 2nd position = WIN
					if ($position == 1 || $position == 2) {
						$winCount++;
					}
					break;

				case 'exacta':
					if ($bet->boxed_flag) {
						// 2 selections any order in 1st or 2nd position = WIN
						if ($position == 1 || $position == 2) {
							$winCount++;
						}
					} else {
						// 2 selections with correct position in 1st & 2nd = WIN
						if ($position > 0 && $selection->position == $position) {
							$winCount++;
						}
					}
					break;

				case 'trifecta':
					if ($bet->boxed_flag) {
						// 3 or more selections any order in 1st-3rd = WIN
						if ($position > 0 && $position < 4) {
							$winCount++;
						}
					} else {
						// 3 or more selections with correct positions in 1st-3rd = WIN
						if ($position > 0 && $selection->position == $position) {
							$winCount++;
						}
					}
					break;

				case 'firstfour':
					if ($bet->boxed_flag) {
						// 4 or more selections any order in 1st-4th = WIN
						if ($position > 0) {
							$winCount++;
						}
					} else {
						// 4 or more selections with correct positions in 1st-4th = WIN
						if ($position > 0 && $selection->position == $position) {
							$winCount++;
						}
					}
					break;

				default :
					break;
			}
		}

		// did we get min number of wins?
		if ($winCount >= $exoticsMinWin[$bet->betType->name]) {
			return true;
		}

		return false;
	}

	public function getExoticDividendForBet(Bet $bet, $exoticName = false)
	{
		if (!$exoticName) {
			return 0;
		}

		$fullDividend = $this->getExoticDividendForEvent($exoticName, $bet->event_id);
		return round(($fullDividend / 100) * ($bet->percentage / 100) * 100, 2);
	}

	public function getExoticDividendForEvent($exoticName, $eventId)
	{
		$exoticDividend = RaceEvent::where('id', $eventId)
				->pluck($exoticName . '_dividend');

		if ($exoticDividend) {
			$uDividend = unserialize($exoticDividend);
			$dividend = array_values($uDividend);
			return str_replace(',', '', $dividend[0]);
		}

		return 0;
	}

	/**
	 * Pass in selections for a new bet and check against a bet for an exact match
	 * 
	 * @param array $selections
	 * @param \TopBetta\Bet $bet
	 * @return boolean
	 */
	public function checkSelectionsMatchExoticBet(array $selections, Bet $bet)
	{
		// build up an array from the existing bet with the same structure as the selections passed in
		$positionMap = array('0' => 'first', '1' => 'first', '2' => 'second', '3' => 'third', '4' => 'fourth');
		$existingSelections = array('first' => array(), 'second' => array(), 'third' => array(), 'fourth' => array());
		
		foreach ($bet->selections as $selection) {
			$existingSelections[$positionMap[$selection['position']]][] = $selection->selection_id;
		}

		// little cleanup first to remove any unused positions
		foreach ($existingSelections as $key => $position) {
			if (count($position) == 0) {
				unset($existingSelections[$key]);
			}
		}

		// check we have an identical array
		return ($selections == $existingSelections) ? true : false;
	}

	public function getFixedOddsForSportsBet(Bet $bet)
	{
		return BetSelection::where('bet_id', $bet->id)
						->where('selection_id', $bet->selections[0]->selection_id)
						->pluck('fixed_odds');
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

        $date = substr(Carbon\Carbon::now(), 0, 10);

        // get current micro time
        list($partMsec, $partSec) = explode(" ", microtime());
        $currentTimeMs = $partSec.$partMsec;
        \File::append('/tmp/'.$date.'-ResultPost-B'. $bet->id.'-R'.$bet->result_transaction_id.'-'.$currentTimeMs, print_r($bet));

		if ($bet->save()) {
			$bet->resultAmount = $amount;
			\TopBetta\RiskManagerAPI::sendBetResult($bet);
			return true;
		}

		return false;
	}

	/**
	 * Refund a bet
	 * 
	 * @param Bet $bet
     * @param Boolean $cancel
	 * @return Boolean
	 */
	public function refundBet(Bet $bet, $cancel = false)
	{
		// Full bet amount was on free credit
		if ($bet->bet_freebet_flag == 1 && $bet->bet_freebet_amount == $bet->bet_amount) {
			$amount = $bet->bet_freebet_amount;
			$bet->refund_freebet_transaction_id = $this->awardFreeBetRefund($bet->user_id, $amount);
		} else if ($bet->bet_freebet_flag == 1 && $bet->bet_freebet_amount < $bet->bet_amount) {
			// Free bet amount was less then refund
			$refundAmount = $bet->bet_amount - $bet->bet_freebet_amount;
			$amount = $bet->bet_amount;
			// Refund free bet amount
			$bet->refund_freebet_transaction_id = $this->awardFreeBetRefund($bet->user_id, $bet->bet_freebet_amount);
			// Refund balance to account
			$bet->refund_transaction_id = $this->awardBetRefund($bet->user_id, $refundAmount);
		} else {
			// No free credit was used - refund full amount to account
			$amount = $bet->bet_amount;
			$bet->refund_transaction_id = $this->awardBetRefund($bet->user_id, $amount);
		}

        // winning bets we take the return/win amount back as well
        if ($bet->bet_result_status_id == BetResultStatus::getBetResultStatusByName('paid') && $bet->result_transaction_id) {
            $this->cancelBetWin($bet);
        }

		$resultStatusId = ($cancel) ? BetResultStatus::STATUS_CANCELLED : BetResultStatus::STATUS_FULL_REFUND;
        $bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName($resultStatusId);
		$bet->refunded_flag = 1;
		$bet->resulted_flag = 1;

		if ($bet->save()) {
			$bet->resultAmount = $amount;
			\TopBetta\RiskManagerAPI::sendBetResult($bet);

            //notify bet refund
            $this->betDashboardNotificationService->notify(array('id' => $bet->id));

			return true;
		}

		return false;
	}

	public function refundBetsForRunnerId($runnerId)
	{
		// refund win/place bets
		$betSelections = BetSelection::where('selection_id', $runnerId)->get();

		foreach ($betSelections as $betSelection) {
			$bet = Bet::where('id', $betSelection->bet_id)
					->where('refunded_flag', 0)
					->where('resulted_flag', 0)
					->first();

			if ($bet && $bet->bet_type_id <= 3) {
				\Log::info("Refunding bet: " . $bet->id . " bet type: " . $bet->bet_type_id);
				$this->refundBet($bet);
			}
		}

		// TODO: partial refund exotic bets

		return true;
	}

    /**
     * Increment a user's account balance
     *
     * @param $user_id
     * @param integer $amount
     * @param string $keyword
     * @internal param object $user
     * @return int
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

    /**
     * Deduct bet win amount from user account balance
     *
     * @param Bet $bet
     * @return bool|int
     */
    private function cancelBetWin(Bet $bet)
    {
        $resultTransaction = $bet->payout;
        if ($resultTransaction && $resultTransaction->transactionType->keyword == 'betwin') {
            // increment with a NEGATIVE amount
            return AccountBalance::_increment($bet->user_id, - $resultTransaction->amount, 'betwincancelled');
        }

        return false;
    }

}
