<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsTicketsController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		$ticketModel = new \TopBetta\TournamentTicket;

		// active tourn tickets
		$activeTicketList = $ticketModel -> getTournamentTicketActiveListByUserID(\Auth::user() -> id);

		$activeTickets = array();

		foreach ($activeTicketList as $activeTicket) {

			$activeTickets[] = array('id' => (int)$activeTicket -> id, 'tournament_id' => (int)$activeTicket -> tournament_id, 'tournament_name' => $activeTicket -> tournament_name, 'buy_in' => (int)$activeTicket -> buy_in, 'entry_fee' => (int)$activeTicket -> entry_fee, 'sport_name' => $activeTicket -> sport_name, 'start_date' => $activeTicket -> start_date, 'end_date' => $activeTicket -> end_date, 'cancelled_flag' => ($activeTicket -> cancelled_flag) ? true : false);

		}

		// recent tourn tickets
		$recentTicketList = $ticketModel -> getTournamentTicketRecentListByUserID(\Auth::user() -> id, time() - 48 * 60 * 60, time(), 1, 't.end_date DESC, t.start_date DESC');

		$recentTickets = array();

		foreach ($recentTicketList as $recentTicket) {

			$recentTickets[] = array('id' => (int)$recentTicket -> id, 'tournament_id' => (int)$recentTicket -> tournament_id, 'tournament_name' => $recentTicket -> tournament_name, 'buy_in' => (int)$recentTicket -> buy_in, 'entry_fee' => (int)$recentTicket -> entry_fee, 'sport_name' => $recentTicket -> sport_name, 'start_date' => $recentTicket -> start_date, 'end_date' => $recentTicket -> end_date, 'cancelled_flag' => ($recentTicket -> cancelled_flag) ? true : false);

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
