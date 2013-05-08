<?php
class UsersController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function login() {
		//
		return View::make('hello');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//

		//TODO: fetch/store the users legacy api cookie in the database
		// - pass this through with each legacy api call if needed
		if (Input::get('action') == 'login') {
			$l = new LegacyApi;
			$login = $l -> query('doUserLogin', array('username' => Input::get('username'), 'password' => 'password'));

			if ($login['status'] == 200) {
				$json = array("success" => true, "result" => array("username" => $login['userInfo']['username'], "name" => $login['userInfo']['name'], "account_type" => $login['userInfo']['accountType']));
			} else {
				$json = array("success" => false, "error" => $login['error_msg']);
			}
			
		} else {
			$l = new LegacyApi;
			$user = $l -> query('getUser', array('username' => Input::get('username')));

			if ($user['status'] == 200) {
				$json = $user;
			} else {
				$json = array("success" => false, "error" => $user['error_msg']);
			}			
			
		}
		
		return Response::json($json);
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
