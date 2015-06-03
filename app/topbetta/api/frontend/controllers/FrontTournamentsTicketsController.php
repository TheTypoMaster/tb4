<?php
namespace TopBetta\frontend;

use Carbon\Carbon;
use Auth;
use TopBetta;
use Illuminate\Support\Facades\Input;
use TopBetta\Services\Tournaments\TournamentBuyInService;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\DashboardNotification\TournamentDashboardNotificationService;
use TopBetta\Services\Tournaments\Exceptions\TournamentBuyInException;
use TopBetta\Services\Tournaments\TournamentLeaderboardService;

class FrontTournamentsTicketsController extends \BaseController {

    /**
     * @var UserAccountService
     */
    private $userAccountService;
    /**
     * @var TournamentDashboardNotificationService
     */
    private $tournamentDashboardNotificationService;
    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var TournamentLeaderboardService
     */
    private $leaderboardService;


    public function __construct(UserAccountService $userAccountService,
                                TournamentDashboardNotificationService $tournamentDashboardNotificationService,
                                TournamentBuyInService $tournamentService,
                                TournamentLeaderboardService $leaderboardService)
    {
		$this -> beforeFilter('auth');
        $this->userAccountService = $userAccountService;
		$this->tournamentDashboardNotificationService = $tournamentDashboardNotificationService;
        $this->tournamentService = $tournamentService;
        $this->leaderboardService = $leaderboardService;
    }

	public function nextToJump() {

		$userId = \Auth::user() -> id;

		$ticketModel = new \TopBetta\TournamentTicket;
		$leaderboardModel = new \TopBetta\TournamentLeaderboard;

		// active tourn tickets
		$activeTicketList = $ticketModel -> getTournamentTicketActiveListByUserID($userId);

		$nextToJump = array();

		if (count($activeTicketList) > 0) {

			foreach ($activeTicketList as $activeTicket) {

				//get the event group from the tournament id
				$tournament = \TopBetta\Tournament::find($activeTicket -> tournament_id);

				$availableCurrency = $ticketModel -> getAvailableTicketCurrency($activeTicket -> tournament_id, \Auth::user() -> id);


				$leaderboardDetails = $this->leaderboardService->getLeaderboardRecordWithPositionForUser(\Auth::user()->id, $activeTicket -> tournament_id);

				$rank = (! $leaderboardDetails['position']) ? '-' : (int)$leaderboardDetails['position'];

				$numEntries = \TopBetta\TournamentTicket::countTournamentEntrants($activeTicket -> tournament_id);

				// get next event for this event group
				$nextData = \TopBetta\TournamentTicket::nextEventForEventGroup($tournament -> event_group_id);

				$next = (count($nextData) > 0) ? $nextData[0] : false;

				if ($next) {

					$nextToJump[] = array('id' => (int)$activeTicket -> id, 'tournament_id' => (int)$activeTicket -> tournament_id, 'type' => ($next -> type) ? strtolower($next -> type) : $next -> sport_name, 'meeting_id' => (int)$next -> meeting_id, 'tournament_name' => $activeTicket -> tournament_name, 'meeting_name' => $next -> meeting_name, 'state' => $next -> state, 'race_number' => (int)$next -> number, 'event_id' => (int)$next -> id, 'event_name' => $next -> name, 'to_go' => \TimeHelper::nicetime(strtotime($next -> start_date), 2), 'start_datetime' => \TimeHelper::isoDate($next -> start_date), 'distance' => $next -> distance, 'leaderboard_rank' => $rank, 'available_currency' => $availableCurrency, 'num_entries' => (int)$numEntries, 'buy_in' => $activeTicket->buy_in, 'closed_betting_on_first_match_flag' => $activeTicket->closed_betting_on_first_match_flag, 'reinvest_winnings_flag' => $activeTicket->reinvest_winnings_flag, 'tournament_sponsor_name' => $activeTicket->tournament_sponsor_name);

				}
			}

		}

		//sort next to jump with earlist first
		usort($nextToJump, function($a, $b) {
			$ad = new \DateTime($a['start_datetime']);
			$bd = new \DateTime($b['start_datetime']);

			if ($ad == $bd) {
				return 0;
			}

			// '>' for earliest first - '<' for latest first
			return $ad > $bd ? 1 : -1;
		});

		return array('success' => true, 'result' => $nextToJump);

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($tournamentId) {

		// special case to load a ticket for a specified tournament
		if ($tournamentId != 'get') {

			$tournamentModel = new \TopBetta\Tournament;
			$tournament = $tournamentModel -> find($tournamentId);

			if (is_null($tournament)) {

				return array('success' => false, 'error' => \Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId)));

			}

			$ticketModel = new \TopBetta\TournamentTicket;

			// Check if we have a ticket in the tournament before going any further
			$myTicketID = $ticketModel->getTournamentTicketByUserAndTournamentID(\Auth::user()->id, $tournamentId);
			if(!$myTicketID){
				return array('success' => false, 'error' => \Lang::get('tournaments.ticket_not_found', array('tournamentId' => $tournamentId)));
			}

			$unregisterAllowed = $ticketModel->unregisterAllowed($tournamentId, $myTicketID[0]->id);
			$unregisterAllowed = $unregisterAllowed->allowed;			
			
			$availableCurrency = $ticketModel -> getAvailableTicketCurrency($tournamentId, \Auth::user() -> id);

            $leaderboardDetails = $this->leaderboardService->getLeaderboardRecordWithPositionForUser(\Auth::user()->id, $tournamentId);

			$prize = 0;
			if (!$tournament -> cancelled_flag && $tournament -> result_transaction_id) {
				if ($recentTicket -> jackpot_flag) {

					$transactionRecord = \TopBetta\FreeCreditBalance::find($tournament -> result_transaction_id);

				} else {

					$transactionRecord = \TopBetta\AccountBalance::find($tournament -> result_transaction_id);
				}

				if ($transactionRecord && $transactionRecord -> amount > 0) {

					$prize = $transactionRecord -> amount;
				}
			}

			$rank = ( ! $leaderboardDetails['position'] ) ? 'N/Q' : (int) $leaderboardDetails['position'];

            // get sport name for tournament ticket
            $sport_name = \TopBetta\SportsSportName::getSportsNameByID($tournament->tournament_sport_id);

            $rebuyAvailable = (bool) $leaderboardDetails['leaderboard']->currency == 0 &&
                ($leaderboardDetails['leaderboard']->balance_to_turnover <= $leaderboardDetails['leaderboard']->turned_over) &&
                Carbon::now() <= $tournament->rebuy_end;

            return array('success' => true, 'result' => array(
                'id' => (int)$myTicketID[0]->id,
				'tournament_id' => (int)$tournamentId,
				'tournament_name' => $tournament -> name,
				'buy_in' => (int)$tournament -> buy_in,
				'entry_fee' => (int)$tournament -> entry_fee,
				'start_currency' => (int)$tournament -> start_currency,
				'available_currency' => $availableCurrency,
				'turned_over' => (int)$leaderboardDetails['leaderboard'] -> turned_over,
                'turnover_remaining' => max($leaderboardDetails['leaderboard']->balance_to_turnover - $leaderboardDetails['leaderboard'] -> turned_over, 0),
				'leaderboard_rank' => $rank,
				'prize' => $prize,
				'qualified' => ($leaderboardDetails['leaderboard']->balance_to_turnover <= $leaderboardDetails['leaderboard']->turned_over) ? true : false,
				'sport_name' => $sport_name,
				'start_date' => \TimeHelper::isoDate($tournament -> start_date),
				'end_date' => \TimeHelper::isoDate($tournament -> end_date),
				'cancelled_flag' => ($tournament -> cancelled_flag) ? true : false,
				'unregister_allowed' => $unregisterAllowed,

                //rebuy info
                'rebuy_available' => $rebuyAvailable,
                'rebuys' => $tournament->rebuys,
                'rebuy_currency' => $tournament->rebuy_currency,
                'rebuy_entry' => $tournament->rebuy_entry,
                'rebuy_buyin' => $tournament->rebuy_buyin,
                'rebuy_end' => $tournament->rebuy_end,
                'ticket_rebuys' => $myTicketID[0]->rebuy_count,

                //topup info
                'tournament_topups' => $tournament->topups,
                'topup_currency' => $tournament->topup_currency,
                'topup_entry' => $tournament->topup_entry,
                'topup_buyin' => $tournament->topup_buyin,
                'topup_end_date' => $tournament->topup_end_date,
                'topup_start_date' => $tournament->topup_start_date,
                'ticket_topups' => $myTicketID[0]->topup_count,

			));

		}


		$userId = \Auth::user() -> id;

		$ticketModel = new \TopBetta\TournamentTicket;

		// active tourn tickets
		$activeTicketList = $ticketModel -> getTournamentTicketActiveListByUserID($userId);

		$activeTickets = array();

		foreach ($activeTicketList as $activeTicket) {

			$availableCurrency = $ticketModel -> getAvailableTicketCurrency($activeTicket -> tournament_id, $userId);

			$tournamentModel = new \TopBetta\Tournament;
			$tournament = $tournamentModel -> find($activeTicket -> tournament_id);

			$leaderboardModel = new \TopBetta\TournamentLeaderboard;
			$leaderboardDetails = $this->leaderboardService->getLeaderboardRecordWithPositionForUser($userId, $activeTicket -> tournament_id);

			$rank = (! $leaderboardDetails['position']) ? '-' : (int)$leaderboardDetails ['position'];

			$unregisterAllowed = $ticketModel->unregisterAllowed($activeTicket -> tournament_id, $activeTicket -> id);
			$unregisterAllowed = $unregisterAllowed->allowed;

            $rebuyAvailable = (bool) $leaderboardDetails['leaderboard']->currency == 0 &&
                ($leaderboardDetails['leaderboard']->balance_to_turnover <= $leaderboardDetails['leaderboard']->turned_over) &&
                Carbon::now() <= $tournament->rebuy_end;

			$activeTickets[] = array('id' => (int)$activeTicket -> id, 'tournament_id' => (int)$activeTicket -> tournament_id, 'tournament_name' => $activeTicket -> tournament_name, 'buy_in' => (int)$activeTicket -> buy_in, 'entry_fee' => (int)$activeTicket -> entry_fee, 'start_currency' => (int)$activeTicket -> start_currency, 'available_currency' => $availableCurrency, 'turned_over' => (int)$leaderboardDetails['leaderboard'] -> turned_over, 'leaderboard_rank' => $rank, 'qualified' => (($leaderboardDetails['leaderboard']->balance_to_turnover <= $leaderboardDetails['leaderboard']->turned_over)) ? true : false, 'sport_name' => $activeTicket -> sport_name, 'start_date' => \TimeHelper::isoDate($activeTicket -> start_date), 'end_date' => \TimeHelper::isoDate($activeTicket -> end_date), 'cancelled_flag' => ($activeTicket -> cancelled_flag) ? true : false, 'unregister_allowed' => $unregisterAllowed,
                 'turnover_remaining' => max($leaderboardDetails['leaderboard']->balance_to_turnover - $leaderboardDetails['leaderboard'] -> turned_over, 0),
                //rebuy info
                 'rebuy_available' => $rebuyAvailable,
                 'rebuys' => $tournament->rebuys,
                 'rebuy_currency' => $tournament->rebuy_currency,
                 'rebuy_entry' => $tournament->rebuy_entry,
                 'rebuy_buyin' => $tournament->rebuy_buyin,
                 'rebuy_end' => $tournament->rebuy_end,
                 'ticket_rebuys' => $activeTicket->rebuy_count,

                //topup info
                 'topups' => $tournament->topups,
                 'topup_currency' => $tournament->topup_currency,
                 'topup_entry' => $tournament->topup_entry,
                 'topup_buyin' => $tournament->topup_buyin,
                 'topup_end_date' => $tournament->topup_end_date,
                 'topup_start_date' => $tournament->topup_start_date,
                 'ticket_topups' => $activeTicket->topup_count,
            );

		}

		// recent tourn tickets
		$recentTicketList = $ticketModel -> getTournamentTicketRecentListByUserID(\Auth::user() -> id, time() - 48 * 60 * 60, time(), 1, 't.end_date DESC, t.start_date DESC');
		//$recentTicketList = $ticketModel -> getTournamentTicketRecentListByUserID(\Auth::user() -> id, null, time(), 1, 't.end_date DESC, t.start_date DESC');

		$recentTickets = array();

		foreach ($recentTicketList as $recentTicket) {

			$availableCurrency = $ticketModel -> getAvailableTicketCurrency($recentTicket -> tournament_id, \Auth::user() -> id);

			$tournamentModel = new \TopBetta\Tournament;
			$tournament = $tournamentModel -> find($recentTicket -> tournament_id);

			$leaderboardModel = new \TopBetta\TournamentLeaderboard;
			$leaderboardDetails = $this->leaderboardService->getLeaderboardRecordWithPositionForUser($userId, $recentTicket -> tournament_id);

			$prize = 0;
			if (!$recentTicket -> cancelled_flag && $recentTicket -> result_transaction_id) {
				if ($recentTicket -> jackpot_flag) {

					$transactionRecord = \TopBetta\FreeCreditBalance::find($recentTicket -> result_transaction_id);

				} else {

					$transactionRecord = \TopBetta\AccountBalance::find($recentTicket -> result_transaction_id);
				}

				if ($transactionRecord && $transactionRecord -> amount > 0) {

					$prize = $transactionRecord -> amount;
				}
			}

			$rank = (! $leaderboardDetails['position']) ? 'N/Q' : (int)$leaderboardDetails['position'];

            $rebuyAvailable = (bool) $leaderboardDetails['leaderboard']->currency == 0 &&
                ($leaderboardDetails['leaderboard']->balance_to_turnover <= $leaderboardDetails['leaderboard']->turned_over) &&
                Carbon::now() <= $tournament->rebuy_end;

			$recentTickets[] = array('id' => (int)$recentTicket -> id, 'tournament_id' => (int)$recentTicket -> tournament_id, 'tournament_name' => $recentTicket -> tournament_name, 'buy_in' => (int)$recentTicket -> buy_in, 'entry_fee' => (int)$recentTicket -> entry_fee, 'start_currency' => (int)$recentTicket -> start_currency, 'available_currency' => $availableCurrency, 'turned_over' => (int)$leaderboardDetails['leaderboard'] -> turned_over, 'leaderboard_rank' => $rank, 'prize' => $prize, 'qualified' => ($leaderboardDetails['leaderboard']->balance_to_turnover <= $leaderboardDetails['leaderboard']->turned_over) ? true : false, 'sport_name' => $recentTicket -> sport_name, 'start_date' => \TimeHelper::isoDate($recentTicket -> start_date), 'end_date' => \TimeHelper::isoDate($recentTicket -> end_date), 'cancelled_flag' => ($recentTicket -> cancelled_flag) ? true : false, 'unregister_allowed' => false,

                 'turnover_remaining' => max($leaderboardDetails['leaderboard']->balance_to_turnover - $leaderboardDetails['leaderboard'] -> turned_over, 0),
                //rebuy info
                 'rebuy_available' => $rebuyAvailable,
                 'rebuys' => $tournament->rebuys,
                 'rebuy_currency' => $tournament->rebuy_currency,
                 'rebuy_entry' => $tournament->rebuy_entry,
                 'rebuy_buyin' => $tournament->rebuy_buyin,
                 'rebuy_end' => $tournament->rebuy_end,
                 'ticket_rebuys' => $recentTicket->rebuy_count,

                //topup info
                 'topups' => $tournament->topups,
                 'topup_currency' => $tournament->topup_currency,
                 'topup_entry' => $tournament->topup_entry,
                 'topup_buyin' => $tournament->topup_buyin,
                 'topup_end_date' => $tournament->topup_end_date,
                 'topup_start_date' => $tournament->topup_start_date,
                 'ticket_topups' => $recentTicket->topup_count,
            );

		}

		return array('success' => true, 'result' => array('active' => $activeTickets, 'recent' => $recentTickets));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {

		$tournaments = Input::json() -> all();

		$messages = array();
		$errors = 0;

		foreach ($tournaments['tournaments'] as $tournamentId) {

			$newRateLimiter = new TopBetta\APIRateLimiter('10', '0', '-ticketPurchase-'.\Auth::user()->id .'-'.$tournamentId, '2');
			$checkRateLimit = $newRateLimiter->RateLimiter();
			
			// if were not rate limited
			if(!$checkRateLimit) {

				// save tournament tickets via legacy API
				$l = new \TopBetta\LegacyApiHelper;
				$tournDetailsArray = array("id" => $tournamentId);
				
				// Add free creidt flag to payload if it's been set on the client
				if (isset($tournaments['use_free_credit'])) {
					$tournDetailsArray['chkFreeBet'] = $tournaments['use_free_credit'];
				}
				
				$ticket = $l -> query('saveTournamentTicket', $tournDetailsArray);
	
				if ($ticket['status'] == 200) {
                    //get the tournament
                    $tournament = \TopBetta\Tournament::find($tournamentId);

                    //decrease turnover balance
                    $this->userAccountService->decreaseBalanceToTurnOver(
                        \Auth::user()->id,
                        $tournament->buy_in + $tournament->entry_fee,
                        true
                    );

                    //store buyin history record
                    $this->tournamentService->createTournamentEntryHistoryRecord($ticket['ticket_id'], $ticket['transactions']['buyin_transaction'], $ticket['transactions']['entry_transaction']);

                    $this->tournamentDashboardNotificationService->notify(array("id" => $ticket['ticket_id'], "transactions" => $ticket['transactions'], "free-credit-transactions" => $ticket['free-credit-transactions']));

					$messages[] = array("id" => $tournamentId, "success" => true, "result" => $ticket['success']);
	
				} elseif ($ticket['status'] == 401) {
	
					return \Response::json(array("success" => false, "error" => "Please login first."), 401);
	
				} elseif ($ticket['status'] == 500) {
	
					$messages[] = array("id" => $tournamentId, "success" => false, "error" => $ticket['error_msg']);
					$errors++;
	
				} else {
	
					return array("success" => false, "error" => $ticket, "status" => $ticket['status']);
	
				}
			}else{
				return \Response::json(array("success" => false, "error" => \Lang::get('tournaments.existing_ticket')), 429);
			}
		}

		return array("success" => ($errors > 0) ? false : true, ($errors > 0) ? "error" : "result" => $messages);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * NOTE: if you can't use DELETE VERB
	 * Add: ?_method=DELETE to requesting POST URL
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($tournamentId, $ticketId) {

		if (is_null($tournamentId)) {
			return array('success' => false, 'error' => \Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId)));
		} else {

			// DO THEY HAVE A TICKET FOR THIS TOURNAMENT
			$ticket = \TopBetta\TournamentTicket::where('id', '=', $ticketId)
				->where('user_id', '=', \Auth::user() -> id)
				->where('refunded_flag', 0)
				->get();

			if(count($ticket) > 0) {

				$ticketModel = new \TopBetta\TournamentTicket;
				$unregisterAllowed = $ticketModel->unregisterAllowed($tournamentId, $ticketId);
				if($unregisterAllowed->allowed) {
					// REFUND TICKET
					$refunded = $ticketModel->refundTicket($ticket[0], true);

					if ($refunded) {
						$leaderboardModel = new \TopBetta\TournamentLeaderboard;
						$leaderboardModel->deleteByUserAndTournamentID(\Auth::user() -> id, $tournamentId);
						
						return array('success' => true, 'result' => \Lang::get('tournaments.refunded_ticket', array('ticketId' => $ticketId)));
					} else {
						return array('success' => false, 'error' => \Lang::get('tournaments.refund_ticket_problem', array('ticketId' => $ticketId)));
					}						
					
				} else {
					return array('success' => false, 'error' => $unregisterAllowed->error);
				}
				
			} else {
				return array('success' => false, 'error' => \Lang::get('tournaments.ticket_not_found'));
			}			
		}
	}

    public function rebuy($ticketId)
    {
        try{
            if( ! $this->tournamentService->ticketBelongsToUser($ticketId, Auth::user()->id) ) {
                return array("success" => false, 'error' => "Tournament ticket does not belong to current user");
            }

            $this->tournamentService->rebuyIntoTournament($ticketId);
        } catch (TournamentBuyInException $e) {
            return array('success' => false, 'error' => $e->getMessage());
        } catch (\Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }

        return array('success' => 'true', 'result' => 'Rebuy successful');
    }

    public function topup($ticketId)
    {
        try{
            if( ! $this->tournamentService->ticketBelongsToUser($ticketId, Auth::user()->id) ) {
                return array("success" => false, 'error' => "Tournament ticket does not belong to current user");
            }

            $this->tournamentService->topupTournament($ticketId);
        } catch (TournamentBuyInException $e) {
            return array('success' => false, 'error' => $e->getMessage());
        } catch (\Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }

        return array('success' => 'true', 'result' => 'Topup successful');
    }

}
