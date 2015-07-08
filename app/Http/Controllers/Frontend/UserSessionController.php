<?php namespace TopBetta\Http\Controllers\Frontend;

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/01/15
 * File creation time: 14:04
 * Project: tb4
 */

use TopBetta\Http\Controllers\Controller;
use Input;
use TopBetta\Models\UserModel;
use Validator;
use Auth;

use Regulus\ActivityLog\Models\Activity;

use TopBetta\Http\Libraries\GetClientDetails;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Authentication\UserAuthenticationService;


class UserSessionController extends Controller {

    protected $response;
    protected $userservice;
    protected $clientDetails;

    public function __construct(ApiResponse $response,
                                UserAuthenticationService $userservice,
                                GetClientDetails $clientDetails)
    {
        $this->response = $response;
        $this->userservice = $userservice;
        $this->clientDetails = $clientDetails;
    }

    public function login(){

        $input = Input::json()->all();

        $rules = array('username' => 'required', 'password' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) return $this->response->failed($validator->messages()->all(), 400, 101, 'User Login Failed', 'User Login Failed - check errors');

        // topbetta currently has MD5 hashed passwords
        try{
            $userDetails = $this->userservice->checkMD5PasswordUser($input['username'], $input['password']);
        }catch (ValidationException $e){
            return $this->response->failed($e->getErrors(), 400, 102, 'Login details incorrect', 'Login details incorrect');
        }

        if( ! $userDetails['activated_flag'] ) {
            return $this->response->failed(array(), 400, 0, "Account not activated", "Account not activated");
        }

        $user = Auth::loginUsingId($userDetails['id']);

      //  $ua = $this->clientDetails->getBrowser();
      //  $user_details = "Browser: " . $ua['name'] . ", Version: " . $ua['version'] . ", Platform: " .$ua['platform'] . ", User Agent:" . $ua['userAgent'];

		if (Auth::check()) {
			// record the logout to the activity table
			Activity::log([
				'contentId'   => Auth::user()->id,
				'contentType' => 'User',
				'action'      => 'Log In',
				'description' => 'User logged into TopBetta',
				'details'     => 'Username: '.Auth::user()->username, //. ' - '.$user_details,
				//'updated'     => $id ? true : false,
			]);
		}

        return $this->response->success($user->load('topbettaUser'));

    }

    public function logout(){

		if (Auth::check()) {
			// record the logout to the activity table
			Activity::log([
				'contentId'   => Auth::user()->id,
				'contentType' => 'User',
				'action'      => 'User Logged Out',
				'description' => 'User logged out of TopBetta',
				'details'     => 'Username: '.Auth::user()->username,
				//'updated'     => $id ? true : false,
			]);
		}

        Auth::Logout();

        if (Auth::check()) {
            return $this->response->failed(array(), '200', 103, 'There was a problem with logging out', 'There was a problem with logging out');
        }
        return $this->response->success(array('You have been logged out!'), 200);
    }

    public function user()
    {
        if( ! Auth::check() ) {
            return $this->response->failed(array(), '401', 100, "Not logged in", "Please login first");
        }

        //get authenticated user
        $user = Auth::user();
        $response = $user->toArray();

        //add extra fields
        $response['account_balance'] = $user->accountBalance();
        $response['free_credit_balance'] = $user->freeCreditBalance();
        $response['balance_to_turnover'] = $user->topbettauser ? $user->topbettauser->balance_to_turnover : 0;

        return $this->response->success($response);
    }
}