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

				$activeBets[] = array('id' => (int)$activeBet -> id, 'freebet' => ($activeBet -> freebet) ? true : false, 'type' => (int)$activeBet -> bet_type, 'result_status' => $activeBet -> result_status, 'event_id' => (int)$activeBet -> event_id, 'event_name' => $activeBet -> event_name, 'event_number' => (int)$activeBet -> event_number, 'selection_id' => (int)$activeBet -> selection_id, 'selection_name' => $activeBet -> selection_name, 'selection_number' => (int)$activeBet -> selection_number, 'bet_total' => (int)$activeBet -> bet_total);

			}

			// recent live bets
			$recentBetList = $betModel -> getRecentLiveBetsForUserId(\Auth::user() -> id, time() - 48 * 60 * 60, time(), 1, 'e.start_date DESC');
			//$recentBetList = $betModel -> getRecentLiveBetsForUserId(\Auth::user() -> id, null, null, 1, 'e.start_date DESC');

			$recentBets = array();

			foreach ($recentBetList as $recentBet) {

				$recentBets[] = array('id' => (int)$recentBet -> id, 'freebet' => ($recentBet -> freebet) ? true : false, 'type' => (int)$recentBet -> bet_type, 'result_status' => $recentBet -> result_status, 'event_id' => (int)$recentBet -> event_id, 'event_name' => $recentBet -> event_name, 'event_number' => (int)$recentBet -> event_number, 'selection_id' => (int)$recentBet -> selection_id, 'selection_name' => $recentBet -> selection_name, 'selection_number' => (int)$recentBet -> selection_number, 'bet_total' => (int)$recentBet -> bet_total, 'win_amount' => (int)$recentBet -> win_amount, 'refund_amount' => (int)$recentBet -> refund_amount);

			}

		} else if ($type == 'tournament') {

			$activeBets = array();
			$recentBets = array();

		} else {

			return array("success" => false, "error" => "Invalid Type");

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
		$rules = array('amount' => 'required|integer', 'source' => 'required|alpha', 'type_id' => 'required|integer');
		$input = Input::json() -> all();

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			// POST bet data to legacy API
			//$l = new \TopBetta\LegacyApiHelper;
			$betModel = new \TopBetta\Bet;

			$messages = array();
			$errors = 0;

			switch ($input['type_id']) {
				case 1 :
					// win

					foreach ($input['selections'] as $selection) {

						// assemble bet data such as meeting_id, race_id etc
						$legacyData = $betModel -> getLegacyBetData($selection);

						if (count($legacyData) > 0) {

							$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => 1, 'value' => $input['amount'], 'selection' => $selection, 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'wager_id' => $legacyData[0] -> wager_id);
							$this -> placeBet($betData, $messages, $errors);

						} else {

							$messages[] = array("id" => $selection, "success" => false, "error" => "selection not found");
							$errors++;

						}

					}

					break;

				case 2 :
					// place

					foreach ($input['selections'] as $selection) {

						// assemble bet data such as meeting_id, race_id etc
						$legacyData = $betModel -> getLegacyBetData($selection);

						if (count($legacyData) > 0) {

							$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => 2, 'value' => $input['amount'], 'selection' => $selection, 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'wager_id' => $legacyData[0] -> wager_id);
							$this -> placeBet($betData, $messages, $errors);

						} else {

							$messages[] = array("id" => $selection, "success" => false, "error" => "selection not found");
							$errors++;

						}

					}

					break;

				default :
					break;
			}

			return array("success" => ($errors > 0) ? false : true, ($errors > 0) ? "error" : "result" => $messages);

		}

	}

	/**
	 * Place the bet via the legacy api, generally called within a list of bets
	 *
	 * @param $betData array
	 * @param $messages array
	 * @param $errors int
	 *
	 */
	private function placeBet($betData, &$messages, &$errors) {

		$l = new \TopBetta\LegacyApiHelper;

		$bet = $l -> query('saveBet', $betData);

		if ($bet['status'] == 200) {

			$messages[] = array("id" => $betData['selection'], "success" => true, "result" => $bet['success']);

		} else {

			$messages[] = array("id" => $betData['selection'], "success" => false, "error" => $bet['error_msg']);
			$errors++;

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
