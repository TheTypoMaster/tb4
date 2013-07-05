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
		$tournamentBetList = $betModel -> getTournamentBetListByTicketID($ticketId);

		$bets = array();

		foreach ($tournamentBetList as $tournamentBet) {

			$bets[] = array('id' => (int)$tournamentBet -> id, 'ticket_id' => (int)$tournamentBet -> tournament_ticket_id, 'event_id' => (int)$tournamentBet -> event_id, 'type' => (int)$tournamentBet -> bet_type, 'market_id' => $tournamentBet -> market_id, 'market' => $tournamentBet -> market_name, 'selection_id' => (int)$tournamentBet -> selection_id, 'selection_name' => $tournamentBet -> selection_name, 'bet_amount' => (int)$tournamentBet -> bet_amount, 'fixed_odds' => (float)$tournamentBet -> fixed_odds, 'win_odds' => (float)$tournamentBet -> win_odds, 'place_odds' => (float)$tournamentBet -> place_odds, 'result_status' => $tournamentBet -> bet_status, 'created_date' => \TimeHelper::isoDate($tournamentBet -> created_date));

		}

		return array('success' => true, 'result' => $bets);
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
