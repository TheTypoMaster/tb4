<?php

namespace TopBetta\Repositories;

use TopBetta\Models\AccountBalance;
use TopBetta\Models\Bet;
use TopBetta\Models\BetResultStatus;
use TopBetta\Models\BetSelection;
use TopBetta\Models\FreeCreditBalance;
use TopBetta\Models\RaceEvent;
use TopBetta\Models\RaceResult;
use TopBetta\Services\Betting\SelectionService;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\DashboardNotification\BetDashboardNotificationService;

use TopBetta\Models\SportsSelectionResults;
use Carbon;

/**
 * Description of BetRepo
 *
 * @author mic
 */
class BetRepo
{

    /**
     * @var UserAccountService
     */
    private $userAccountService;
	/**
	 * @var BetDashboardNotificationService
     */
    private $betDashboardNotificationService;
    /**
     * @var SelectionService
     */
    private $selectionService;

    public function __construct(UserAccountService $userAccountService, BetDashboardNotificationService $betDashboardNotificationService, SelectionService $selectionService)
    {
        $this->userAccountService = $userAccountService;
		$this->betDashboardNotificationService = $betDashboardNotificationService;
        $this->selectionService = $selectionService;
    }

	/**
	 * Lookup results for the selections made for this bet
	 * Return the payout amount in cents if a winning bet
	 * 
	 * @param Bet $bet
	 * @return int (cents)
	 */
	public function getBetPayoutAmount(Bet $bet, $freeBetOnly = false)
	{
		$payout = 0;
		$dividend = 0;

		switch ($bet->betType->name) {
			case 'win':
				if ($bet->bet_origin_id == 2 && $this->selectionService->isSelectionRacing($bet->selections[0]->selection_id)) {
					// RACING: simple check - do we have a result record for this selection id and win dividend
					$dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
							->where('win_dividend', '>', 0)
							->value('win_dividend');
				} else if ($bet->bet_origin_id == 3 && $this->selectionService->isSelectionSports($bet->selections[0]->selection_id)) {
					// SPORTS: check for a result record
					$win = SportsSelectionResults::selectionResultExists($bet->selections[0]->selection_id);

					// Get fixed odd if it's a win
					if ($win) {
						$dividend = $this->getFixedOddsForSportsBet($bet);
					}
				} else {
                    \Log::error("BET RESULT: " . $bet->id . " invalid origin or origin mismatch, origin: " . $bet->bet_origin_id . " selection: " . $bet->selections[0]->selection_id);
                }
				break;

			case 'place':
				// simple check: do we have a result record for this selection id and place dividend
				$dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
						->where('place_dividend', '>', 0)
						->value('place_dividend');
				break;

			// EXOTICS
			case 'quinella':
				$payout = $this->getExoticPayoutForBet($bet, 'quinella', $freeBetOnly) * 100;
				break;

			case 'exacta':
				$payout = $this->getExoticPayoutForBet($bet, 'exacta', $freeBetOnly) * 100;
				break;

			case 'trifecta':
				$payout = $this->getExoticPayoutForBet($bet, 'trifecta', $freeBetOnly) * 100;
				break;

			case 'firstfour':
				$payout = $this->getExoticPayoutForBet($bet, 'firstfour', $freeBetOnly) * 100;
				break;

			default:
				break;
		}

		// CALC THE RETURN AMOUNT IF IT WAS A WINNING BET
		if ($dividend > 0) {
			//$payout = $bet->bet_amount * round($dividend, 2);
			$payout = bcmul($freeBetOnly ? $bet->bet_freebet_amount : $bet->bet_amount, round($dividend, 2));
		}

		return (int) $payout;
	}

    public function getBaseDividendForBet(Bet $bet)
    {
        $payout = 0;
        $dividend = 0;

        switch ($bet->betType->name) {
            case 'win':
                if ($bet->bet_origin_id == 2) {
                    // RACING: simple check - do we have a result record for this selection id and win dividend
                    $dividend = RaceResult::where('selection_id', $bet->selections[0]->selection_id)
                        ->where('win_dividend', '>', 0)
                        ->value('win_dividend');
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
                    ->value('place_dividend');
                break;

            // EXOTICS
            case 'quinella':
                if ($this->checkWinningExoticbet($bet)) {
                    $dividend = $this->getExoticDividendForBet($bet, 'quinella');
                }
                break;

            case 'exacta':
                if ($this->checkWinningExoticbet($bet)) {
                    $dividend = $this->getExoticDividendForBet($bet, 'exacta');
                }
                break;

            case 'trifecta':
                if ($this->checkWinningExoticbet($bet)) {
                    $dividend = $this->getExoticDividendForBet($bet, 'trifecta');
                }
                break;

            case 'firstfour':
                if ($this->checkWinningExoticbet($bet)) {
                    $dividend = $this->getExoticDividendForBet($bet, 'firstfour');
                }
                break;

            default:
                break;
        }

        return $dividend;
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
					->value('position');

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

    public function getExoticPayoutForBet(Bet $bet, $exoticName = false, $freeBetOnly = false) {
        //get dividend
        $fullDividend = $this->getExoticDividendForBet($bet, $exoticName);

        //get percentage
        $percentage = $freeBetOnly ? bcdiv($bet->bet_freebet_amount, $bet->combinations, 2) : $bet->percentage;

        //($percentage/100) * 100 = $percentage!
        return round(($fullDividend / 100) * ($percentage / 100) * 100, 2);
    }

	/**
	 * Calculates exotic dividend for a given exotic bet.
	 * @param Bet $bet
	 * @param bool $exoticName
	 * @return float|int
	 */
	public function getExoticDividendForBet(Bet $bet, $exoticName = false)
	{
		if(!$exoticName) {
			return 0;
		}

		$dividends = $this->getExoticDividendsForEvent($exoticName, $bet->event_id);

        if( ! $dividends ) { return 0; }

		//Should be a function in BetSelectionRepo or BetSelectionService for this.
		//Gets the selection for a bet and organises them in to an array by position selected
		$betSelection = array_map( function($value) {
			return explode(",", $value);
		}, explode("/", BetSelection::getExoticSelectionsForBetid($bet->id)));

		$fullDividend = 0;

		foreach( $dividends as $placeGetters => $dividend ) {
			$placeGettersArray = explode("/", $placeGetters);

			//Bet is boxed so only need to make sure the bet has the selections
			if( $bet->boxed_flag && count(array_intersect($placeGettersArray, $betSelection[0])) == count($placeGettersArray) ) {
				$fullDividend += $dividend;
			} else if ( ! $bet->boxed_flag ) {
				//Bet is not boxed so make sure positions are correct
				$pays = true;
				foreach($placeGettersArray as $key => $place) {
					//does the bet have the selection in this position
					if( ! in_array($place, $betSelection[$key]) ) {
						$pays = false;
						break;
					}
				}

				//A selection has been found for each position so the bet is a winner
				if( $pays ) {
					$fullDividend += $dividend;
				}
			}
		}

		return $fullDividend;
	}

	public function getExoticDividendForEvent($exoticName, $eventId)
	{
		$exoticDividend = RaceEvent::where('id', $eventId)
				->value($exoticName . '_dividend');

		if ($exoticDividend) {
			$uDividend = unserialize($exoticDividend);
			$dividend = array_values($uDividend);
			return str_replace(',', '', $dividend[0]);
		}

		return 0;
	}

	public function getExoticDividendsForEvent($exoticName, $eventId)
	{
		$exoticDividend = RaceEvent::where('id', $eventId)
			->value($exoticName . '_dividend');

		if ($exoticDividend) {
			return unserialize($exoticDividend);

		}

		return 0;
	}

	/**
	 * Pass in selections for a new bet and check against a bet for an exact match
	 *
	 * @param array $selections
	 * @param \TopBetta\Models\Bet $bet
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
						->value('fixed_odds');
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

            \Log::info("AMOUNT: " . $this->getBetPayoutAmount($bet, true) . " freebet " . $bet->bet_freebet_amount);

            $this->userAccountService->addFreeCreditWinsToTurnOver(
                $bet->user_id,
                max($this->getBetPayoutAmount($bet, true) - $bet->bet_freebet_amount, 0)
            );
		}

		$bet->result_transaction_id = $this->awardBetWin($bet->user_id, $amount);

		$bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_PAID);
		$bet->resulted_flag = 1;

        $date = substr(Carbon\Carbon::now(), 0, 10);

        // get current micro time
        list($partMsec, $partSec) = explode(" ", microtime());
        $currentTimeMs = $partSec.$partMsec;
        \File::append('/tmp/'.$date.'-ResultPost-B'. $bet->id.'-R'.$bet->result_transaction_id.'-'.$currentTimeMs, print_r($bet, true));

		if ($bet->save()) {
			$bet->resultAmount = $amount;
			\TopBetta\Helpers\RiskManagerAPI::sendBetResult($bet);
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
			\TopBetta\Helpers\RiskManagerAPI::sendBetResult($bet);

            //notify bet refund
            $this->betDashboardNotificationService->notify(array('id' => $bet->id, 'notification_type' => 'bet_refund'));

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
            $transactionId = AccountBalance::_increment($bet->user_id, - $resultTransaction->amount, 'betwincancelled');

            if($transactionId) {
                $this->betDashboardNotificationService->notify(array("id" => $bet->id, "transactions" => array($transactionId)));
            }

            return $transactionId;
        }

        return false;
    }

}
