<?php namespace TopBetta\Http\Frontend\Controllers;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontUsersProfileController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($username) {

		//TODO: fetch the topbetta user object
		$l = new \TopBetta\LegacyApiHelper;
		$user = $l -> query('getUser', array('username' => \Auth::user() -> username));

		if ($user['status'] == 200) {

			return array("success" => true, "result" => array('id' => $user['id'], "username" => $user['username'], "first_name" => $user['first_name'], "last_name" => $user['last_name'], "email" => \Auth::user() -> email, "full_account" => $user['tb_user']));

		} else {

			return array("success" => false, "error" => $user['error_msg']);

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

		//validate our data first
		$input = Input::json() -> all();

		//password is 6-12 char and must include a number
		$rules = array('password' => array('between:6,12', 'regex:([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])'), 'confirm_password' => 'required_with:password|same:password', 'jackpot_flag' => 'in:0,1');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			$errors = array();
			$messages = array();

			//updated our password if requried
			if (isset($input['password'])) {

				//we need the legacy API to generate a Joomla password
				$joomlaPassword = false;
				$l = new \TopBetta\LegacyApiHelper;
				$pwd = $l -> query('generateJoomlaPassword', array('password' => $input['password']));

				if ($pwd['status'] == 200) {

					$joomlaPassword = $pwd['joomla_password'];

				} else {

					$errors[] = \Lang::get('users.problem_saving_password');

				}

				if ($joomlaPassword) {

					$user = \User::find(\Auth::user() -> id);

					$user -> password = $joomlaPassword;

					$user -> save();

					$messages[] = \Lang::get('users.password_changed');

					$audit = new \TopBetta\UserAudit( array('user_id' => \Auth::user() -> id, 'admin_id' => -1, 'field_name' => 'password', 'old_value' => '*', 'new_value' => '*', 'update_date' => date("Y-m-d H:i:s")));
					$audit -> save();

				}

			}

			//handle our jackpot flag
			if (isset($input['jackpot_flag'])) {

				$tbUser = \TopBetta\TopBettaUser::where('user_id', '=', \Auth::user() -> id) -> take(1) -> get();

				$oldFlag = $tbUser[0] -> email_jackpot_reminder_flag;

				if ($oldFlag != $input['jackpot_flag']) {

					$tbUser[0] -> email_jackpot_reminder_flag = $input['jackpot_flag'];

					$tbUser[0] -> save();

					$messages[] = \Lang::get('users.jackpot_flag_set');

					$audit = new \TopBetta\UserAudit( array('user_id' => \Auth::user() -> id, 'admin_id' => -1, 'field_name' => 'email_jackpot_reminder_flag', 'old_value' => $oldFlag, 'new_value' => $input['jackpot_flag'], 'update_date' => date("Y-m-d H:i:s")));
					$audit -> save();

				}

			}

			if (count($errors)) {

				return array("success" => false, "error" => $errors);

			} else {

				return array("success" => true, "result" => $messages);

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
