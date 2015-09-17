<?php namespace TopBetta\Http\Controllers\Frontend;

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/01/15
 * File creation time: 14:04
 * Project: tb4
 */

use TopBetta\Http\Controllers\Controller;
use Input;
use TopBetta\Resources\UserResource;
use TopBetta\Services\Exceptions\UnauthorizedAccessException;
use Auth;

use Regulus\ActivityLog\Models\Activity;

use TopBetta\Http\Libraries\GetClientDetails;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Authentication\UserAuthenticationService;
use TopBetta\Services\Validation\Exceptions\ValidationException;


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

    public function loginExternal(){

        $input = Input::json()->all();

        try {
            $user = $this->userservice->login($input);
        } catch (ValidationException $e) {
            return $this->response->failed($e->getErrors(), 400, 101, 'User Login Failed', 'User Login Failed - check errors');
        } catch (UnauthorizedAccessException $e) {
            return $this->response->failed($e->getMessage(), 401);
        } catch (\Exception $e) {
            \Log::error("UserSessionController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success($user);

    }

    public function login()
    {
        $input = Input::json()->all();

        try {
            $user = $this->userservice->login($input);
        } catch (ValidationException $e) {
            return $this->response->failed($e->getErrors(), 400, 101, 'User Login Failed', 'User Login Failed - check errors');
        } catch (UnauthorizedAccessException $e) {
            return $this->response->failed($e->getMessage(), 401);
        } catch (\Exception $e) {
            \Log::error("UserSessionController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success($user->toArray());
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


        return $this->response->success($user->toArray());
    }
}