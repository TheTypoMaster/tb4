<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontBetsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		// return active bets & recent bets

		// active SQL
		$query = "SELECT
	      		b.*,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		e.id AS event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.id AS selection_id,
	      		s.name AS selection_name,
	      		s.number AS selection_number,
	      		bat.amount AS bet_total
			FROM
				tbdb_bet AS b
			INNER JOIN
				tbdb_bet_type AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				tbdb_bet_result_status AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				tbdb_bet_selection AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				tbdb_selection AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				tbdb_market AS m
			ON
				m.id = s.market_id
			INNER JOIN
				tbdb_event AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				tbdb_account_transaction AS bat
			ON
				bat.id = b.bet_transaction_id
			WHERE
				b.user_id = $userId
			AND
				b.resulted_flag = 0
			
			GROUP BY
				b.id";

		return 'Get users bets';
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

						// assemble win bet data such as meeting_id, race_id etc
						$legacyData = $betModel -> getLegacyBetData($selection);

						if (count($legacyData) > 0) {

							$betData = array('id' => $legacyData[0] -> meeting_id, 'race_id' => $legacyData[0] -> race_id, 'bet_type_id' => 1, 'value' => $input['amount'], 'selection' => $selection, 'pos' => $legacyData[0] -> number, 'bet_origin' => $input['source'], 'bet_product' => 5, 'wager_id' => $legacyData[0] -> wager_id);
							$this -> placeBet($betData, $messages, $errors);
							/*
							 $bet = $l -> query('saveBet', $betData);

							 if ($bet['status'] == 200) {

							 $messages[] = array("id" => $selection, "success" => true, "result" => $bet['success']);

							 } else {

							 $messages[] = array("id" => $selection, "success" => false, "error" => $bet['error_msg']);
							 $errors++;

							 }
							 */

						} else {

							$messages[] = array("id" => $selection, "success" => false, "error" => "selection not found");
							$errors++;

						}

					}

					break;

				case 2 :
					// place

					break;

				default :
					break;
			}

			return array("success" => ($errors > 0) ? false : true, ($errors > 0) ? "error" : "result" => $messages);

		}

	}

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
