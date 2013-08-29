<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class FrontUsersController extends \BaseController {

	public function __construct() {

		//we are only protecting certain routes in this controller
		$this -> beforeFilter('auth', array('only' => array('index')));

	}

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

					if (!$login['userInfo']['full_account']) {

						$parts = explode(" ", \Auth::user()->name);
						$lastname = array_pop($parts);
						$firstname = implode(" ", $parts);

					} else {

						$lastname = $login['userInfo']['last_name'];
						$firstname = $login['userInfo']['first_name'];

					}

					$tbUser = \TopBetta\TopBettaUser::where('user_id', '=', \Auth::user()->id) -> first();

					$mobile = NULL;
					$verified = false;

					if ($tbUser){
						$mobile = $tbUser -> msisdn;
						$verified = ($tbUser -> identity_verified_flag) ? true : false;
					}

					return array("success" => true, "result" => array("id" => $login['userInfo']['id'], "username" => $login['userInfo']['username'], "first_name" => ucwords($firstname), "last_name" => ucwords($lastname), "email" => \Auth::user()->email, "mobile" => $mobile, "full_account" => $login['userInfo']['full_account'], "verified" => $verified, "register_date" => \TimeHelper::isoDate(\Auth::user()->registerDate)));

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

		$action = \Input::get('action');

		switch ($action) {
			case 'exclude' :

				//forward to legacy API to handle
				$l = new \TopBetta\LegacyApiHelper;

				$exclude = $l -> query('doSelfExclude', $input = array());

				if ($exclude['status'] == 200) {

					//log this user out of laravel - joomla logout is done via legacy api
					$this -> logout();
					return array('success' => true, 'result' => $exclude['msg']);

				} else {

					return array('success' => false, 'error' => $exclude['error_msg']);

				}
				break;

			default :
				break;
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

		$input = Input::json() -> all();

		$rules = array('first_name' => 'required|alpha_num|min:3', 'last_name' => 'required|alpha_num|min:3', 'source' => 'required|alpha_dash', 'type' => 'required|in:basic,upgrade,full');

		//shared between upgrade & full accounts
		$extRules = array('title' => 'required|in:Mr,Mrs,Ms,Miss,Dr,Prof', 'dob_day' => 'required|max:2', 'dob_month' => 'required|max:2', 'dob_year' => 'required|max:4', 'phone' => 'required|min:9', 'postcode' => 'required|max:6', 'street' => 'required|max:100', 'city' => 'required|max:50', 'state' => 'required|max:50', 'country' => 'required|alpha|max:3', 'promo_code' => 'alpha_dash|max:100', 'heard_about' => 'alpha_dash|max:200', 'heard_about_info' => 'alpha_dash|max:200', 'optbox' => 'in:0,1,true,false', 'privacy' => 'accepted', 'terms' => 'accepted');

		if ($input['type'] == 'basic') {

			$rules['email'] = 'required|email|unique:tbdb_users';
			$rules['mobile'] = 'required|min:9';
			$rules['password'] = array('required', 'min:5', 'regex:([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])');

		}

		if ($input['type'] == 'upgrade') {

			$rules = array_merge($rules, $extRules);

		}

		if ($input['type'] == 'full') {

			$extRules['username'] = 'unique:tbdb_users';
			$rules = array_merge($rules, $extRules);

		}

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			// create user via legacy API
			$l = new \TopBetta\LegacyApiHelper;

			if ($input['type'] == 'basic') {

				$user = $l -> query('doUserRegisterBasic', $input);

			} elseif ($input['type'] == 'upgrade') {

				$user = $l -> query('doUserUpgradeTopBetta', $input);

			} elseif ($input['type'] == 'full') {

				$user = $l -> query('doUserRegisterTopBetta', $input);

			}

			if ($user['status'] == 200) {

				if ($input['type'] != 'upgrade') {

					return array("success" => true, "result" => \Lang::get('users.account_created', array('username' => $user['username'])));

				} else {

					return array("success" => true, "result" => \Lang::get('users.account_upgraded'));

				}

			} else {

				if ($input['type'] == 'basic') {

					return array("success" => false, "error" => $user['error_msg'] . str_replace("<br>", " ", $user['errors']));

				} else {

					return array("success" => false, "error" => $user['error_msg']);

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
