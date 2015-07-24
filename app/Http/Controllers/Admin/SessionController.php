<?php namespace TopBetta\Http\Controllers\Admin;

use Cartalyst\Sentry\Users\UserNotFoundException;
use TopBetta\Http\Controllers\Controller;

use Auth;
use Input;
use Redirect;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use Validator;
use View;
use Sentry;

use TopBetta\Models\UserModel;
use TopBetta\Services\Authentication\UserAuthenticationService;



class SessionController extends Controller
{
	protected $authentication;

	public function __construct(UserAuthenticationService $authentication){
		$this->authentication = $authentication;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (Auth::check()) {
            $user = $user = Sentry::findUserByLogin(Auth::user()->username);

            if( $user->hasAccess('admin.*') ) {
                // TODO: would be nice for user to set home page
                return Redirect::to('/admin/dashboard');
            }
		}

		return View::make('admin.sessions.create');
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

            //find the sentry user
            try {
                    $user = Sentry::findUserByLogin($input['username']);
                } catch( UserNotFoundException $e ) {
                    return Redirect::back()->with('flash_message', 'Invalid credentials OR your account is disabled')->withInput();
            }

			if ( $user->hasAccess('admin.*') ) {

				try{
					$login = $this->authentication->checkMD5PasswordUser($input['username'], $input['password']);
				}catch(ValidationException $e){
					return Redirect::back()->with('flash_message', 'Invalid credentials OR your account is disabled')->withInput();
				}

				$attempt = Auth::loginUsingId($login['id']);

				if ($attempt) {
					return Redirect::intended('/admin/dashboard')->with('flash_message', 'You are now logged in!');
				}

				return Redirect::back()->with('flash_message', 'Invalid credentials OR your account is disabled')->withInput();
			}

            return Redirect::back()->with('flash_message', 'Access Denied');
		}

        return Redirect::back()->with('flash_message', 'Please supply username and password');
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
