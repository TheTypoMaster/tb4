<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class FrontUsersController extends \BaseController {

	public function login() {

		$input = Input::json() -> all();

		$rules = array('username' => 'required', 'password' => 'required');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {
			
			$l = new \TopBetta\LegacyApiHelper;
			$login = $l -> query('doUserLogin', $input);

			if ($login['status'] == 200) {

				// we do a standard laravel auth with the joomla user id in the DB
				\Auth::loginUsingId($login['userInfo']['id']);

				if (\Auth::check()) {

					return array("success" => true, "result" => array("id" => $login['userInfo']['id'], "username" => $login['userInfo']['username'], "first_name" => $login['userInfo']['first_name'], "last_name" => $login['userInfo']['last_name'], "full_account" => $login['userInfo']['full_account']));

				} else {

					return array("success" => false, "error" => Lang::get('users.login_problem'));

				}

			} else {

				return array("success" => false, "error" => $login['error_msg']);

			}

		}

	}

	public function logout() {

		//logout of laravel only
		\Auth::logout();

		if (\Auth::check()) {

			return array("success" => false, "error" => Lang::get('users.logout_problem'));

		} else {

			//kill our laravel session which joomla is relying on
			\Session::regenerate();

			return array("success" => true, "result" => Lang::get('users.logout_success'));

		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//
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
