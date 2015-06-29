<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

class FrontUsersReferController extends Controller {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		return array("success" => true, "result" => array('referral_id' => \Auth::user() -> id, 'referral_link' => secure_url('user/refer/' . \Auth::user() -> id)));

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
		//validate our data first
		$input = \Input::json() -> all();

		$rules = array('friend_email' => 'required|email', 'subject' => 'required', 'message' => 'required');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			$l = new \TopBetta\Helpers\LegacyApiHelper;
			$refer = $l -> query('doReferFriend', $input);

			if ($refer['status'] == 200) {

				return array("success" => true, "result" => $refer['msg']);

			} else {

				return array("success" => false, "error" => $refer['error_msg']);

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
