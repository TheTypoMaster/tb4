<?php

namespace TopBetta\Repositories;

use TopBetta\Models\Bet;
use TopBetta\Models\BetResultStatus;
use TopBetta\Models\BetSelection;
use TopBetta\Models\RaceEvent;
use TopBetta\Models\RaceResult;

use Carbon;
use Log;
use TopBetta\Services\DashboardNotification\BetDashboardNotificationService;

use TopBetta\Services\Betting\ExternalSourceBetNotificationService;
use TopBetta\Services\UserAccount\UserAccountService;

/**
 * Description of BetResult
 *
 * @author mic
 */
class BetResultRepo
{

    const TURNOVER_MIN_AMOUNT = 1.5;

    /**
     * @var BetDashboardNotificationService
     */
    private $dashboardNotificationService;


	protected $notifications;
    /**
     * @var UserAccountService
     */
    private $userAccountService;

    function __construct(ExternalSourceBetNotificationService $notifications, UserAccountService $userAccountService, BetDashboardNotificationService $dashboardNotificationService)
	{
		$this->notifications = $notifications;
        $this->userAccountService = $userAccountService;
		 $this->dashboardNotificationService = $dashboardNotificationService;
    }


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
		// TODO: remove sport bets from this list
//		$events = Bet::where('bet_result_status_id', 1)
//				->where('resulted_flag', 0)
//				->where('tbdb_event.event_status_id', 2)
//				->orWhere('tbdb_event.event_status_id', 4)
//				->join('tbdb_event', 'tbdb_bet.event_id', '=', 'tbdb_event.id')
//				->groupBy('tbdb_bet.event_id')
//				->select('tbdb_bet.event_id')
//				->get();

        $events = Bet::where('bet_result_status_id', 1)
                                ->where('resulted_flag', 0)
                                // ->whereNotNull('tbdb_event.number') //TODO - this removes sports....
                                ->where(function($query)
                                {
                                    $query->where('tbdb_event.event_status_id', 2)
                                        ->orWhere('tbdb_event.event_status_id', 4);
                                })
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
		// we only want bets that are "unresulted" status id: 1 or partially refunded, status id: 3
		$bets = Bet::where('event_id', $eventId)
				->whereIn('bet_result_status_id', array(1,3))
				->where('resulted_flag', 0)
				->with('selections')
				->get();

		$result = array();
        $date = substr(Carbon\Carbon::now(), 0, 10);

		foreach ($bets as $bet) {

            // get current micro time
            list($partMsec, $partSec) = explode(" ", microtime());
            $currentTimeMs = $partSec.$partMsec;
            \File::append('/tmp/'.$date.'-ResultPost-E'. $eventId. '-B'.$bet->id.'-'.$currentTimeMs, print_r($bet,true));


			Log::info('RESULTING BET: ' . $bet->id);
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

		$eventStatus = RaceEvent::where('id', $bet->event_id)->value('event_status_id');

		// RACE ABANDONED - REFUND BET
		if ($eventStatus == 3) {
			Log::info('ABANDONED: refunding bet: ' . $bet->id);
			return \TopBetta\Facades\BetRepo::refundBet($bet);
		}

		$resultModel = new RaceResult;
		$raceResults = $resultModel->getResultsForRaceId($bet->event_id);

		// Sanity check - Make sure we at least have a win_dividend
		if (!isset($raceResults['positions'][1]['win_dividend'])) {
			Log::info('NO WIN DIVIDEND: EventID - ' . $bet->event_id);
			return false;
		}

		// RACE PAYING INTERIM/FINAL
		if ($eventStatus == 6 && $bet->betType->name == 'win') {
			// RULE 1: Status interim - Result all "Winning" bets for Win, leave the ones that didn't win in case there is a protest
			$processBet = true;
		} elseif ($eventStatus == 2 || $eventStatus == 4) {
			// RULE 2: Status paying - Result all other bets at Final Dividends
			$processBet = true;
			$bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_PAID);
			$bet->resulted_flag = 1;
		}

		if (!$processBet) {
			Log::info('NOT Processing Bet: ' . $bet->id.', Event Status: '.$eventStatus);
			return false;
		}


		$resultBet = $this->processBetPayout($bet);

		if($resultBet) {
            $this->dashboardNotificationService->notify(array("id" => $bet->id, 'notification_type' => 'bet_resulted'));
			$this->notifications->notifyBetResult($bet);
        }

		return $resultBet;

	}

	/**
	 * Find all pending bets for a market and result them
	 *
	 * @param type $extMarketId
	 * @return type
	 */
	public function resultAllSportBetsForMarket($extMarketId)
	{

		$bets = Bet::where('bet_result_status_id', 1)
				->join('tbdb_bet_selection as bs', 'bs.bet_id', '=', 'tbdb_bet.id')
				->join('tbdb_selection as s', 'bs.selection_id', '=', 's.id')
				->where('resulted_flag', 0)
				->where('s.external_market_id', $extMarketId)
				->select('tbdb_bet.*')
				->get();

		$result = array();

		foreach ($bets as $bet) {
			Log::info('RESULTING SPORT BET: ' . $bet->id);
			$result[$bet->id] = $this->resultSportBet($bet);
		}

		return $result;
	}

	/**
	 * Result an individual sport bet object
	 *
	 * @param Bet $bet
	 * @return bool
	 */
	public function resultSportBet(Bet $bet)
	{
		$processBet = false;

		// TODO: do we need to check if event was abandoned or ready to payout

		// TODO: handle refunds
		$bet->bet_result_status_id = BetResultStatus::getBetResultStatusByName(BetResultStatus::STATUS_PAID);
		$bet->resulted_flag = 1;
		$processBet = true;

		if (!$processBet) {
			return false;
		}

		$result = $this->processBetPayout($bet);

        if($result) {
            $this->dashboardNotificationService->notify(array("id" => $bet->id, 'notification_type' => 'bet_resulted'));
        }

        return $result;
	}	
	
	private function processBetPayout(Bet $bet) {
		$payout = \TopBetta\Facades\BetRepo::getBetPayoutAmount($bet);
		Log::info('PAYOUT FOR BET: id ' . $bet->id . ' : ' . $payout);


        $date = substr(Carbon\Carbon::now(), 0, 10);

        // get current micro time
        list($partMsec, $partSec) = explode(" ", microtime());
        $currentTimeMs = $partSec.$partMsec;
        \File::append('/tmp/'.$date.'-ResultPost-' .'B'. $bet->id.'-'.$currentTimeMs, print_r($bet,true). " - Payout :". $payout);



        if ($payout) {
			// WINNING BET
			Log::info('WINNING BET: id - ' . $bet->id);

            //update user turnover
            $this->userAccountService->decreaseBalanceToTurnOver($bet->user_id, $bet->bet_amount - $bet->bet_freebet_amount, \TopBetta\Facades\BetRepo::getBaseDividendForBet($bet) >= self::TURNOVER_MIN_AMOUNT);

            return \TopBetta\Facades\BetRepo::payoutBet($bet, $payout);
		}

		// if we get here, the bet was not a winning bet or not refunded
		if ($bet->save()) {

            //update user turnover
            $this->userAccountService->decreaseBalanceToTurnOver($bet->user_id, $bet->bet_amount - $bet->bet_freebet_amount, true);

			$bet->resultAmount = 0;
			Log::info('LOSING BET: ' . $bet->id);
			\TopBetta\Helpers\RiskManagerAPI::sendBetResult($bet);
			return true;
		}	
		
		return false;
	}
}
