<?php

namespace TopBetta\admin\controllers;

use Auth;
use Input;
use Redirect;
use Validator;
use User;
use View;

class SessionController extends \BaseController
{

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (Auth::check()) {
			// TODO: would be nice for user to set home page
			return Redirect::to('/admin/dashboard');
		}

		return View::make('sessions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// validate
		$input = Input::all();

		$validation = Validator::make($input, array(
					'username' => 'required',
					'password' => 'required'
		));

		if ($validation->passes()) {

			// make sure they are a joomla admin user (gid == 25) before attempting login
			if (User::where('username', $input['username'])->pluck('gid') == 25) {
				// legacy login
				$l = new \TopBetta\LegacyApiHelper;
				$login = $l->query('doUserLogin', $input);

				if ($login['status'] == 200) {

					$attempt = Auth::loginUsingId($login['userInfo']['id']);

					if ($attempt) {
						return Redirect::intended('/admin/dashboard')->with('flash_message', 'You are now logged in!');
					}
				}
			}
		}

		return Redirect::back()->with('flash_message', 'Invalid credentials OR your account is disabled')->withInput();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy()
	{
		Auth::logout();
		return Redirect::home()->with('flash_message', 'Logged out');
	}

}
