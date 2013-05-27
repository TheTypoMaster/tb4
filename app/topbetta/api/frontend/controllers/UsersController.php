<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class UsersController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//

		//TODO: fetch/store the users legacy api cookie in the laravel session
		// - pass this through with each legacy api call if needed
		if (Input::get('action') == 'login') {
				
			$l = new \TopBetta\LegacyApiHelper;
			$login = $l -> query('doUserLogin', array('username' => Input::get('username'), 'password' => 'password'));

			if ($login['status'] == 200) {
					
				// TODO: store the username/id in the laravel session
				return array("success" => true, "result" => array("id" => $login['userInfo']['id'], "username" => $login['userInfo']['username'], "first_name" => $login['userInfo']['first_name'], "last_name" => $login['userInfo']['last_name'], "full_account" => $login['userInfo']['full_account']));

			} else {

				return array("success" => false, "error" => $login['error_msg']);

			}
		} else {

			// they called this resource without trying to do a login
			return array("success" => false, "error" => "Please login first");
		}

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
