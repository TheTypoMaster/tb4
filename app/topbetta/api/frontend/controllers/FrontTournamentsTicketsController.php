<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsTicketsController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	public function nextToJump() {

		$userId = \Auth::user() -> id;

		$ticketModel = new \TopBetta\TournamentTicket;

		// active tourn tickets
		$activeTicketList = $ticketModel -> getTournamentTicketActiveListByUserID($userId);

		$nextToJump = array();

		if (count($activeTicketList) > 0) {

			foreach ($activeTicketList as $activeTicket) {

				//get the event group from the tournament id
				$tournament = \TopBetta\Tournament::find($activeTicket -> tournament_id);

				// get next event for this event group
				$nextData = \TopBetta\TournamentTicket::nextEventForEventGroup($tournament -> event_group_id);

				$next = (count($nextData) > 0) ? $nextData[0] : false;

				if ($next) {

					$nextToJump[] = array('id' => (int)$next -> id, 'tournament_id' => $activeTicket -> tournament_id, 'type' => $next -> type, 'meeting_id' => (int)$next -> meeting_id, 'meeting_name' => $next -> meeting_name, 'state' => $next -> state, 'race_number' => (int)$next -> number, 'to_go' => \TimeHelper::nicetime(strtotime($next -> start_date), 2), 'start_datetime' => \TimeHelper::isoDate($next -> start_date), 'distance' => $next -> distance);

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
	public function index() {

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
			$leaderboardDetails = $leaderboardModel -> getLeaderBoardRankByUserAndTournament($userId, $tournament);

			$rank = ($leaderboardDetails -> rank == 0) ? '-' : (int)$leaderboardDetails -> rank;

			$activeTickets[] = array('id' => (int)$activeTicket -> id, 'tournament_id' => (int)$activeTicket -> tournament_id, 'tournament_name' => $activeTicket -> tournament_name, 'buy_in' => (int)$activeTicket -> buy_in, 'entry_fee' => (int)$activeTicket -> entry_fee, 'available_currency' => $availableCurrency, 'turned_over' => (int)$leaderboardDetails -> turned_over, 'leaderboard_rank' => $rank, 'qualified' => ($leaderboardDetails -> qualified) ? true : false, 'sport_name' => $activeTicket -> sport_name, 'start_date' => \TimeHelper::isoDate($activeTicket -> start_date), 'end_date' => \TimeHelper::isoDate($activeTicket -> end_date), 'cancelled_flag' => ($activeTicket -> cancelled_flag) ? true : false);

		}

		// recent tourn tickets
		$recentTicketList = $ticketModel -> getTournamentTicketRecentListByUserID(\Auth::user() -> id, time() - 48 * 60 * 60, time(), 1, 't.end_date DESC, t.start_date DESC');
		//$recentTicketList = $ticketModel -> getTournamentTicketRecentListByUserID(\Auth::user() -> id, null, time(), 1, 't.end_date DESC, t.start_date DESC');

		$recentTickets = array();

		foreach ($recentTicketList as $recentTicket) {

			$availableCurrency = $ticketModel -> getAvailableTicketCurrency($activeTicket -> tournament_id, \Auth::user() -> id);

			$tournamentModel = new \TopBetta\Tournament;
			$tournament = $tournamentModel -> find($activeTicket -> tournament_id);

			$leaderboardModel = new \TopBetta\TournamentLeaderboard;
			$leaderboardDetails = $leaderboardModel -> getLeaderBoardRankByUserAndTournament($userId, $tournament);

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

			$rank = ($leaderboardDetails -> rank == "-") ? 'N/Q' : (int)$leaderboardDetails -> rank;

			$recentTickets[] = array('id' => (int)$recentTicket -> id, 'tournament_id' => (int)$recentTicket -> tournament_id, 'tournament_name' => $recentTicket -> tournament_name, 'buy_in' => (int)$recentTicket -> buy_in, 'entry_fee' => (int)$recentTicket -> entry_fee, 'available_currency' => $availableCurrency, 'turned_over' => (int)$leaderboardDetails -> turned_over, 'leaderboard_rank' => $rank, 'prize' => $prize, 'qualified' => ($leaderboardDetails -> qualified) ? true : false, 'sport_name' => $recentTicket -> sport_name, 'start_date' => \TimeHelper::isoDate($recentTicket -> start_date), 'end_date' => \TimeHelper::isoDate($recentTicket -> end_date), 'cancelled_flag' => ($recentTicket -> cancelled_flag) ? true : false);

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

			// save tournament tickets via legacy API
			$l = new \TopBetta\LegacyApiHelper;
			$ticket = $l -> query('saveTournamentTicket', array("id" => $tournamentId));

			if ($ticket['status'] == 200) {

				$messages[] = array("id" => $tournamentId, "success" => true, "result" => $ticket['success']);

			} else {

				$messages[] = array("id" => $tournamentId, "success" => false, "error" => $ticket['error_msg']);
				$errors++;

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
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
	}

}
