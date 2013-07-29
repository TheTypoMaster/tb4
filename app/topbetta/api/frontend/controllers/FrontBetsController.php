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

		$betModel = new \TopBetta\Bet;

		// active live bets
		$activeBetList = $betModel -> getActiveLiveBetsForUserId(\Auth::user() -> id);
		$activeBets = array();

		foreach ($activeBetList as $activeBet) {

			$betGroup = ($activeBet -> origin == 'betting') ? 'racing' : $activeBet -> origin;

			//exotic bet selections
			$exoticSelections = false;

			$odds = null;

			if ($activeBet -> bet_type == 1) {

				$odds = (float)$activeBet -> win_odds;

			} elseif ($activeBet -> bet_type == 2) {

				$odds = (float)$activeBet -> place_odds;

			} elseif ($activeBet -> bet_type > 3) {

				$exoticSelections = true;
				$exoticBetTransaction = \TopBetta\AccountBalance::find($activeBet -> bet_transaction_id);

				if ($exoticBetTransaction) {
					$exoticAmount = abs($exoticBetTransaction -> amount);
				} else {
					$exoticAmount = 0;
				}

			}

			$activeBets[] = array('id' => (int)$activeBet -> id, 'bet_group' => $betGroup, 'freebet' => ($activeBet -> freebet) ? true : false, 'market_id' => (int)$activeBet -> market_id, 'market' => $activeBet -> market_name, 'type' => (int)$activeBet -> bet_type, 'result_status' => $activeBet -> result_status, 'event_id' => (int)$activeBet -> event_id, 'event_name' => $activeBet -> event_name, 'event_number' => (int)$activeBet -> event_number, 'boxed_flag' => ($activeBet -> boxed_flag) ? true: false, 'combinations' => (int)$activeBet -> combinations, 'percentage' => (float)$activeBet -> percentage, ($activeBet -> selection_string) ? 'exotic_selection_string' : 'selection_id' => ($activeBet -> selection_string) ? $activeBet -> selection_string : (int)$activeBet -> selection_id, 'selection_name' => $activeBet -> selection_name, 'selection_number' => (int)$activeBet -> selection_number, 'odds' => $odds, 'bet_amount' => ($exoticSelections) ? $exoticAmount : (int)abs($activeBet -> bet_total), 'freebet_amount' => (int)abs($activeBet -> freebet_amount), 'created_date' => $activeBet -> created_date);

		}

		// recent live bets
		//$recentBetList = $betModel -> getRecentLiveBetsForUserId(\Auth::user() -> id, time() - 48 * 60 * 60, time(), 1, 'e.start_date DESC');
		$recentBetList = $betModel -> getRecentLiveBetsForUserId(\Auth::user() -> id, null, null, 1, 'e.start_date DESC');

		$recentBets = array();

		foreach ($recentBetList as $recentBet) {

			$betGroup = ($recentBet -> origin == 'betting') ? 'racing' : $recentBet -> origin;

			//exotic bet selections
			$exoticSelections = false;
			$odds = null;

			if ($recentBet -> bet_type == 1) {

				$odds = (float)$recentBet -> win_odds;

			} elseif ($recentBet -> bet_type == 2) {

				$odds = (float)$recentBet -> place_odds;

			} elseif ($recentBet -> bet_type > 3) {

				$exoticSelections = true;
				$exoticBetTransaction = \TopBetta\AccountBalance::find($recentBet -> bet_transaction_id);

				$exoticAmount = 0;
				if($exoticBetTransaction) {
					$exoticAmount = abs($exoticBetTransaction -> amount);
				}

				$exoticDividend = \TopBetta\Bet::getExoticDividendForType($recentBet -> bet_type, $recentBet -> event_id);

			}

			$recentBets[] = array('id' => (int)$recentBet -> id, 'bet_group' => $betGroup, 'freebet' => ($recentBet -> freebet) ? true : false, 'market_id' => (int)$recentBet -> market_id, 'market' => $recentBet -> market_name, 'type' => (int)$recentBet -> bet_type, 'result_status' => $recentBet -> result_status, 'event_id' => (int)$recentBet -> event_id, 'event_name' => $recentBet -> event_name, 'event_number' => (int)$recentBet -> event_number, 'boxed_flag' => ($recentBet -> boxed_flag) ? true: false, 'combinations' => (int)$recentBet -> combinations, 'percentage' => (float)$recentBet -> percentage, ($recentBet -> selection_string) ? 'exotic_selection_string' : 'selection_id' => ($recentBet -> selection_string) ? $recentBet -> selection_string : (int)$recentBet -> selection_id, ($exoticSelections) ? 'exotic_dividend' : '' => ($exoticSelections) ? $exoticDividend : '', 'selection_name' => $recentBet -> selection_name, 'selection_number' => (int)$recentBet -> selection_number, 'bet_amount' => ($recentBet -> selection_string) ? $exoticAmount : (int)abs($recentBet -> bet_total), 'freebet_amount' => (int)abs($recentBet -> freebet_amount), 'odds' => $odds, 'win_amount' => (int)$recentBet -> win_amount, 'refund_amount' => (int)$recentBet -> refund_amount, 'created_date' => $recentBet -> created_date);

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

		$input = Input::json() -> all();

		// change these common rules as required
		$rules = array('source' => 'required|alpha');

		if ($input['source'] == 'tournamentsports') {

			$extRules = array('tournament_id' => 'required|integer', 'bets' => 'required');

			$rules = array_merge($rules, $extRules);

			$input['type_id'] = null;

		} elseif ($input['source'] == 'sports') {

			$extRules = array('bets' => 'required');

			$rules = array_merge($rules, $extRules);

			$input['type_id'] = null;

		} elseif ($input['source'] == 'racing') {

			$extRules = array('amount' => 'required|integer', 'source' => 'required|alpha', 'type_id' => 'required|integer', 'flexi' => 'required');

			$rules = array_merge($rules, $extRules);

		}

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

		//TODO: remove tournament bets from here - they belong in FrontTournamentsBetsController

		$l = new \TopBetta\LegacyApiHelper;
		$betModel = new \TopBetta\Bet;

		if ($exotic) {

			$legacyData = $betModel -> getLegacyBetData($input['selections']['first'][0]);

			$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => $input['type_id'], 'value' => $input['amount'], 'selection' => $input['selections'], 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'flexi' => $input['flexi'], 'wager_id' => $legacyData[0] -> wager_id);

			//set our free bet flag if passed in
			if (isset($input['use_free_credit'])) {

					$betData['chkFreeBet'] = $input['use_free_credit'];

			}

			$bet = $l -> query('saveRacingBet', $betData);

			//bet has been placed by now, deal with messages and errors
			if ($bet['status'] == 200) {

				$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => true, "result" => $bet['success']);

			} else {

				$messages[] = array("id" => $betData['selection'], "type_id" => $input['type_id'], "success" => false, "error" => $bet['error_msg']);
				$errors++;

			}

		} else {

			if ($input['source'] == 'racing' OR $input['source'] == 'tournamentracing') {

				// racing

				foreach ($input['selections'] as $selection) {

					// assemble bet data such as meeting_id, race_id etc
					$legacyData = $betModel -> getLegacyBetData($selection);

					if (count($legacyData) > 0) {

						if ($input['source'] == 'racing') {

							$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => $input['type_id'], 'value' => $input['amount'], 'selection' => $selection, 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'flexi' => $input['flexi'], 'wager_id' => $legacyData[0] -> wager_id);

							//set our free bet flag if passed in
							if (isset($input['use_free_credit'])) {

									$betData['chkFreeBet'] = $input['use_free_credit'];

							}

							$bet = $l -> query('saveBet', $betData);

						} elseif ($input['source'] == 'tournamentracing') {

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

			} else {

				//sports

				if ($input['source'] == 'tournamentsports' OR $input['source'] == 'sports') {

					//bets = array('offer_id' => value);

					if ($input['source'] == 'sports') {

						//TODO: this approach is just finding event/market from a single bet selection - this is all we need today
						$legacyData = $betModel -> getLegacySportsBetData(key($input['bets']));

						if (count($legacyData) > 0) {

							$betData = array('match_id' => $legacyData[0] -> event_id, 'market_id' => $legacyData[0] -> market_id, 'bets' => $input['bets']);

							//set our free bet flag if passed in
							if (isset($input['use_free_credit'])) {

									$betData['chkFreeBet'] = $input['use_free_credit'];

							}

							$bet = $l -> query('saveSportBet', $betData);

						} else {

							$messages[] = array("success" => false, "error" => \Lang::get('bets.selection_not_found'));
							$errors++;

							//TODO: temp fix for no selections - could be better
							$bet['status'] = 500;
							$bet['error_msg'] = \Lang::get('bets.selection_not_found');
							$betData['bets'] = $input['bets'];

						}

					} else {

						//TODO: this approach is just finding event/market from a single bet selection - this is all we need today
						$legacyData = $betModel -> getLegacySportsBetData(key($input['bets']));

						if (count($legacyData) > 0) {

							$betData = array('id' => $input['tournament_id'], 'match_id' => $legacyData[0] -> event_id, 'market_id' => $legacyData[0] -> market_id, 'bets' => $input['bets']);
							$bet = $l -> query('saveTournamentSportsBet', $betData);

						} else {

							$messages[] = array("id" => $selection, "success" => false, "error" => \Lang::get('bets.selection_not_found'));
							$errors++;

						}

					}

					//bet has been placed by now, deal with messages and errors
					if ($bet['status'] == 200) {

						$messages[] = array("bets" => $betData['bets'], "type_id" => $input['type_id'], "success" => true, "result" => $bet['success']);

					} else {

						$messages[] = array("bets" => $betData['bets'], "type_id" => $input['type_id'], "success" => false, "error" => $bet['error_msg']);
						$errors++;

					}

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
