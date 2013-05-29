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

		$this -> beforeFilter('auth');

		// change these rules as required
		$rules = array('id' => 'required|integer', 'race_id' => 'required', 'bet_type_id' => 'required', 'value' => 'required|integer', 'selection' => 'required', 'pos' => 'required|integer', 'bet_origin' => 'required|alpha', 'bet_product' => 'required|integer', 'wager_id' => 'required|integer');
		$input = Input::json() -> all();

		$input['username'] = \Auth::user() -> username;

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {
			// POST bet data to legacy API
			$l = new \TopBetta\LegacyApiHelper;
			$bet = $l -> query('saveBet', $input);

			if ($bet['status'] == 200) {
				
				return array('success' => true, 'result' => $bet['success']);
				
			} else {
					
				return array('success' => false, 'error' => $bet['error_msg']);
				
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
