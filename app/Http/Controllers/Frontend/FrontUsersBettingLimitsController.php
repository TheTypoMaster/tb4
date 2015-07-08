<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

class FrontUsersBettingLimitsController extends Controller {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		//if there's a requested bet limit change, get the request date from audit table
		$requested_date = null;

		$requested_log = \TopBetta\Models\UserAudit::getRecentUserAuditByUserIDAndFieldName(\Auth::user() -> id, array('requested_bet_limit', 'bet_limit'));

		if (!is_null($requested_log) && $requested_log -> field_name == 'requested_bet_limit') {

			$requested_date = $requested_log -> update_date;

		}

		$no_limit = true;
		$bet_limit = null;

		// fetch the users bet_limit
		$user = \TopBetta\Models\TopBettaUser::where('user_id', '=', \Auth::user() -> id) -> get();
		$user = $user[0];

		if ($user -> bet_limit >= 0) {

			$no_limit = false;

		}

		$requested_limit_change = null;
		if ($requested_date) {
			$requested_limit_change = 'The request to raise your loss limit to ' . ($user -> requested_bet_limit == -1 ? '(No Limit)' : '$' . bcdiv($user -> requested_bet_limit, 100, 2)) . ' was sent on ' . date('d/m/Y', strtotime($requested_date));
		}

		$limit_message = "A loss limit means that you will not be able to lose an amount greater than this in a 24-hour day.<br>From midnight to midnight (" . date('T') . "), you will be allowed to place bets and enter cash tournaments (provided the funds are available in your account) up to your loss limit. You may also continue to bet with any cash winnings you receive on that day provided your total spend minus winnings remains under your limit.<br>You may lower your loss limit at any time, but raising it will only take effect after 7 days.<br>If you would like to block your access to the site entirely, you can request self-exclusion. Should you require any further information on loss limits or responsible gambling, please check the Help section or contact us.";

		return array('success' => true, 'result' => array('no_limit' => $no_limit, 'bet_limit' => $user -> bet_limit, 'request_limit_change' => $requested_limit_change, 'limit_message' => $limit_message));

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

		$input = \Input::json() -> all();

		$rules = array('no_limit' => 'required', 'bet_limit' => 'integer');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			//forward to legacy API to handle
			$l = new \TopBetta\Helpers\LegacyApiHelper;

			$limit = $l -> query('setBetLimit', $input);

			//bet has been placed by now, deal with messages and errors
			if ($limit['status'] == 200) {

				return array('success' => true, 'result' => $limit['msg']);

			} else {

				return array('success' => false, 'result' => $limit['error_msg']);

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
