<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontBetsController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		$type = Input::get('type', 'live');

		if ($type == 'live') {

			$betModel = new \TopBetta\Bet;

			// active live bets
			$activeBetList = $betModel -> getActiveLiveBetsForUserId(\Auth::user() -> id);

			$activeBets = array();

			foreach ($activeBetList as $activeBet) {

				$betGroup = ($activeBet -> origin == 'betting') ? 'racing' : $activeBet -> origin;

				$activeBets[] = array('id' => (int)$activeBet -> id, 'bet_group' => $betGroup, 'freebet' => ($activeBet -> freebet) ? true : false, 'type' => (int)$activeBet -> bet_type, 'result_status' => $activeBet -> result_status, 'event_id' => (int)$activeBet -> event_id, 'event_name' => $activeBet -> event_name, 'event_number' => (int)$activeBet -> event_number, 'selection_id' => (int)$activeBet -> selection_id, 'selection_name' => $activeBet -> selection_name, 'selection_number' => (int)$activeBet -> selection_number, 'bet_total' => (int)$activeBet -> bet_total, 'created_date' => $activeBet -> created_date, 'invoice_id' => $activeBet -> invoice_id);

			}

			// recent live bets
			$recentBetList = $betModel -> getRecentLiveBetsForUserId(\Auth::user() -> id, time() - 48 * 60 * 60, time(), 1, 'e.start_date DESC');
			//$recentBetList = $betModel -> getRecentLiveBetsForUserId(\Auth::user() -> id, null, null, 1, 'e.start_date DESC');

			$recentBets = array();

			foreach ($recentBetList as $recentBet) {

				$betGroup = ($recentBet -> origin == 'betting') ? 'racing' : $recentBet -> origin;

				$recentBets[] = array('id' => (int)$recentBet -> id, 'bet_group' => $betGroup, 'freebet' => ($recentBet -> freebet) ? true : false, 'type' => (int)$recentBet -> bet_type, 'result_status' => $recentBet -> result_status, 'event_id' => (int)$recentBet -> event_id, 'event_name' => $recentBet -> event_name, 'event_number' => (int)$recentBet -> event_number, 'selection_id' => (int)$recentBet -> selection_id, 'selection_name' => $recentBet -> selection_name, 'selection_number' => (int)$recentBet -> selection_number, 'bet_total' => (int)$recentBet -> bet_total, 'win_amount' => (int)$recentBet -> win_amount, 'refund_amount' => (int)$recentBet -> refund_amount, 'created_date' => $recentBet -> created_date, 'invoice_id' => $recentBet -> invoice_id);

			}

		} else if ($type == 'tournament') {
				
			//lookup this users ticket id for this tournament id	
			$tournamentId = Input::get('tournament_id', null);
			
			if (!$tournamentId) {
				return array("success" => false, "error" => \Lang::get('tournaments.not_found', array('tournamentId', $tournamentId)));	
			}
			
			// \Auth::user() -> id
			// fetch the event_id & ticket_id based on the tournament_id
			$tournament = \TopBetta\Tournament::find($tournamentId);
			$eventId = $tournament->event_group_id;
			
			$ticket = \TopBetta\TournamentTicket::where('tournament_id', '=', $tournamentId) -> where('user_id', '=', \Auth::user() -> id) -> get();			
			//dd($ticket);
			if (!$ticket) {
				
				return array("success" => false, "error" => \Lang::get('tournaments.ticket_not_found'));	
				
			} else {
				
				$ticketId = ($ticket) ? $ticket -> id : null;
				
			}
			
			$betModel = new \TopBetta\Bet;

			// active tournament bets
			$activeTournamentBetList = $betModel -> getTournamentBetListByEventIDAndTicketID($eventId, $ticketId);

			$activeBets = array();

			foreach ($activeTournamentBetList as $activeTournamentBet) {

				$betGroup = ($activeTournamentBet -> origin == 'betting') ? 'racing' : $activeTournamentBet -> origin;

				$activeTournamentBets[] = array('id' => (int)$activeTournamentBet -> id, 'bet_group' => $betGroup, 'freebet' => ($activeTournamentBet -> freebet) ? true : false, 'type' => (int)$activeTournamentBet -> bet_type, 'result_status' => $activeTournamentBet -> result_status, 'event_id' => (int)$activeTournamentBet -> event_id, 'event_name' => $activeTournamentBet -> event_name, 'event_number' => (int)$activeTournamentBet -> event_number, 'selection_id' => (int)$activeTournamentBet -> selection_id, 'selection_name' => $activeTournamentBet -> selection_name, 'selection_number' => (int)$activeTournamentBet -> selection_number, 'bet_total' => (int)$activeTournamentBet -> bet_total, 'created_date' => $activeTournamentBet -> created_date, 'invoice_id' => $activeTournamentBet -> invoice_id);

			}
			$recentBets = array();

		} else {

			return array("success" => false, "error" => \Lang::get('bets.invalid_type'));

		}

		return array('success' => true, 'result' => array('active' => $activeBets, 'recent' => $recentBets));
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

		//TODO: **** WHAT ARE WE DOING WITH BET_PRODUCT (TOTE)?

		// change these rules as required
		$rules = array('amount' => 'required|integer', 'source' => 'required|alpha', 'type_id' => 'required|integer', 'flexi' => 'required');
		$input = Input::json() -> all();

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			$messages = array();
			$errors = 0;
			
			// type id 3 is each way
			if ($input['type_id'] == 3) {
					
				//do our win bets	
				$input['type_id'] = 1;
				$this -> placeBet($input, $messages, $errors);
				
				//do our place bets
				$input['type_id'] = 2;
				$this -> placeBet($input, $messages, $errors);
			
			} elseif ($input['type_id'] < 3) {
				
				$this -> placeBet($input, $messages, $errors);
				
			} else {
				
				$this -> placeBet($input, $messages, $errors, true);
				
			}

			return array("success" => ($errors > 0) ? false : true, ($errors > 0) ? "error" : "result" => $messages);

		}

	}


	/**
	 * Place the bet via the legacy api, generally called within a list of bets
	 *
	 * @param $inout array
	 * @param $messages array
	 * @param $errors int
	 *
	 */
	private function placeBet(&$input, &$messages, &$errors, $exotic = false) {

		$l = new \TopBetta\LegacyApiHelper;
		$betModel = new \TopBetta\Bet;

		if ($exotic) {	
					
				$legacyData = $betModel -> getLegacyBetData($input['selections']['first'][0]);
					
				$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => $input['type_id'], 'value' => $input['amount'], 'selection' => $input['selections'], 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'flexi' => $input['flexi'], 'wager_id' => $legacyData[0] -> wager_id);	
				
				$bet = $l -> query('saveRacingBet', $betData);
				
				//bet has been placed by now, deal with messages and errors						
				if ($bet['status'] == 200) {
		
					$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => true, "result" => $bet['success']);
		
				} else {
		
					$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => false, "error" => $bet['error_msg']);
					$errors++;
		
				}				
							
		} else {
		
			foreach ($input['selections'] as $selection) {
	
				// assemble bet data such as meeting_id, race_id etc
				$legacyData = $betModel -> getLegacyBetData($selection);
	
				if (count($legacyData) > 0) {				
			
					if ($input['source'] == 'racing') {
							
						$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => $input['type_id'], 'value' => $input['amount'], 'selection' => $selection, 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'flexi' => $input['flexi'], 'wager_id' => $legacyData[0] -> wager_id);	
						
						$bet = $l -> query('saveBet', $betData);
							
					} elseif ($input['source'] == 'tournament') {
							
						$betData = array('id' => $input['tournament_id'], 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => $input['type_id'], 'value' => $input['amount'], 'selection' => $selection, 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'wager_id' => $legacyData[0] -> wager_id);
						$bet = $l -> query('saveTournamentBet', $betData);
						
					} else {
							
						//invalid source
						$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => false, "error" => \Lang::get('bets.invalid_source'));
						$errors++;					
						
					}
					
					//bet has been placed by now, deal with messages and errors						
					if ($bet['status'] == 200) {
			
						$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => true, "result" => $bet['success']);
			
					} else {
			
						$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => false, "error" => $bet['error_msg']);
						$errors++;
			
					}				
	
				} else {
	
					$messages[] = array("id" => $selection, "type_id" => $input['type_id'], "success" => false, "error" => \Lang::get('bets.selection_not_found'));
					$errors++;
	
				}
	
			}
		}

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
