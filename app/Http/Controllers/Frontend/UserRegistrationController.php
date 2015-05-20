<?php namespace TopBetta\Http\Frontend\Controllers;

use BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Input;

use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\Response\ApiResponse;

class UserRegistrationController extends BaseController {

	protected $accountservice;
	protected $response;
    /**
     * @var UserDashboardNotificationService
     */
    private $userDashboardNotificationService;

    function __construct(UserAccountService $accountservice,
						 ApiResponse $response,
                         UserDashboardNotificationService $userDashboardNotificationService)
	{
		$this->accountservice = $accountservice;
		$this->response = $response;
        $this->userDashboardNotificationService = $userDashboardNotificationService;
    }


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function createFull()
	{
		$externalWelcomeEmail = Input::get("external_welcome_email", false);
		try{
			$accountCreationResponse = $this->accountservice->createTopbettaUserAccount(Input::json()->all());

			//send the welcome/activation email if external_welcome_email is not specified or false
			if( ! $externalWelcomeEmail ) {
				$this->accountservice->sendWelcomeEmail($accountCreationResponse['id'], Input::get("email_source", null));
			}

            $this->userDashboardNotificationService->notify($accountCreationResponse);

			return $this->response->success($accountCreationResponse);
		}catch(ValidationException $e){
			return $this->response->failed($e->getErrors(), 400, 101, 'User Registration Failed', 'User Registration Failed');
		}
	}

	public function createBasic()
	{
		$externalWelcomeEmail = Input::json("external_welcome_email", false);
		try{
			$accountCreationResponse = $this->accountservice->createBasicAccount(Input::json()->all());

			//send the welcome/activation email if external_welcome_email is not specified or false
			if( ! $externalWelcomeEmail && ! Input::json("auto_activate", false) ) {
				$this->accountservice->sendWelcomeEmail($accountCreationResponse['id'], Input::json("external_source", null));
			}

            $this->userDashboardNotificationService->notify($accountCreationResponse);

			return $this->response->success($accountCreationResponse);
		} catch(ValidationException $e) {
			return $this->response->failed($e->getErrors(), 400, 101, 'User Registration Failed', 'User Registration Failed');
		}
	}

	public function resendWelcomeEmail($userId)
	{
		try {
			$this->accountservice->sendWelcomeEmail($userId, Input::get("external_source", null));
			return $this->response->success(array());
		} catch(ValidationException $e) {
			return $this->response->failed($e->getErrors(), 500, 101, 'Sending failed', 'Sending failed');
		}
	}

	public function activate($activationHash)
	{
		try {
			$user = $this->accountservice->activateUser($activationHash);
		} catch (ModelNotFoundException $e) {
			return $this->response->failed(array(), 500, null, "User Not Found", "User Account not found");
		} catch (\Exception $e) {
			return $this->response->failed($e->getMessage());
		}

        $this->userDashboardNotificationService->notify($user);

		return $this->response->success($user);
	}

	public function createFullChildFromClone()
	{
		try{
			$accountCreationResponse = $this->accountservice->createUniqueChildUserAccount(Input::json()->all());
			return $this->response->success($accountCreationResponse);
		}catch(ValidationException $e){
			return $this->response->failed($e->getErrors(), 400, 102, 'Child User Registration Failed', 'Child User Registration Failed');
		}

	}

}
