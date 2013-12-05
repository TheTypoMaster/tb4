<?php
namespace TopBetta\frontend;

use TopBetta;
use Password;
use Input;

class FrontPasswordResetsController extends \BaseController {
	
    /**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$status = Password::remind(['email' => Input::get('email')], function($message)
		{
			$message->subject('Your Password Reminder');
		});

//		$status = Session::has('error') ? 'Could not find user with that email address.' : 'Please check your email!';

//		return Redirect::route('password_resets.create')->withStatus($status);
                return $status;
	}


	public function postReset()
	{
		$creds = [
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation')
		];

		return Password::reset($creds, function($user, $password)
		{
			$user->password = Hash::make($password);
			$user->save();

//			return Redirect::route('sessions.create');
                        return "Password saved";
		});
	}
}