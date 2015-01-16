<?php namespace TopBetta\Frontend\Controllers;

use BaseController;
use Input;

use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\Response\ApiResponse;

class UserRegistrationController extends BaseController {

	protected $accountservice;
	protected $response;

	function __construct(UserAccountService $accountservice,
						 ApiResponse $response)
	{
		$this->accountservice = $accountservice;
		$this->response = $response;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function createFull()
	{
		try{
			$accountCreationResponse = $this->accountservice->createTopbettaUserAccount(Input::json()->all());
			return $this->response->success($accountCreationResponse);
		}catch(ValidationException $e){
			return $this->response->failed($e->getErrors(), 400, 101, 'User Registration Failed', 'User Registration Failed');
		}

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
