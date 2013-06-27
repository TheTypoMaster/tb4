<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsBetsController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//lookup this users ticket id for this tournament id
		$tournamentId = Input::get('tournament_id', null);

		// fetch the event_id & ticket_id based on the tournament_id
		$tournament = \TopBetta\Tournament::find($tournamentId);

		if (!$tournament) {

			return array("success" => false, "error" => \Lang::get('tournaments.not_found', array('tournamentId', $tournamentId)));

		}

		$eventId = $tournament -> event_group_id;

		$ticket = \TopBetta\TournamentTicket::where('tournament_id', '=', $tournamentId) -> where('user_id', '=', \Auth::user() -> id) -> get();

		if (count($ticket) == 0) {

			return array("success" => false, "error" => \Lang::get('tournaments.ticket_not_found'));

		} else {

			$ticketId = $ticket[0] -> id;

		}

		$betModel = new \TopBetta\TournamentBet;

		// active tournament bets
		$activeTournamentBetList = $betModel -> getTournamentBetListByTicketID($ticketId, false);

		$activeBets = array();

		foreach ($activeTournamentBetList as $activeTournamentBet) {

			//TODO: handle sports and racing - this is mostly sports at the moment
			$activeBets[] = array('id' => (int)$activeTournamentBet -> id, 'ticket_id' => (int)$activeTournamentBet -> tournament_ticket_id, 'event_id' => (int)$activeTournamentBet -> event_id, 'bet_type' => $activeTournamentBet -> bet_type, 'type' => $activeTournamentBet -> market_type, 'selection_id' => (int)$activeTournamentBet -> selection_id, 'selection_name' => $activeTournamentBet -> selection_name, 'bet_amount' => (int)$activeTournamentBet -> bet_amount, 'fixed_odds' => (float)$activeTournamentBet -> fixed_odds, 'win_odds' => (float)$activeTournamentBet -> win_odds, 'place_odds' => (float)$activeTournamentBet -> place_odds, 'result' => '-');

		}

		$resultedTournamentBetList = $betModel -> getTournamentBetListByTicketID($ticketId, true);

		$resultedBets = array();

		foreach ($resultedTournamentBetList as $resultedTournamentBet) {

			//TODO: handle sports and racing - this is mostly sports at the moment
			$resultedBets[] = array('id' => (int)$resultedTournamentBet -> id, 'ticket_id' => (int)$resultedTournamentBet -> tournament_ticket_id, 'event_id' => (int)$resultedTournamentBet -> event_id, 'bet_type' => $resultedTournamentBet -> bet_type, 'type' => $resultedTournamentBet -> market_type, 'selection_id' => (int)$resultedTournamentBet -> selection_id, 'selection_name' => $resultedTournamentBet -> selection_name, 'bet_amount' => (int)$resultedTournamentBet -> bet_amount, 'fixed_odds' => (float)$resultedTournamentBet -> fixed_odds, 'win_odds' => (float)$resultedTournamentBet -> win_odds, 'place_odds' => (float)$resultedTournamentBet -> place_odds, 'paid' => (int)$resultedTournamentBet -> win_amount, 'result' => ($resultedTournamentBet -> win_amount > 0) ? 'WIN' : 'LOSS');

		}

		return array('success' => true, 'result' => array('active' => $activeBets, 'resulted' => $resultedBets));
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
		//
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
