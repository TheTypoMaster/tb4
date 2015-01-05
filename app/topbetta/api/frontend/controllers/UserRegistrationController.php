<?php namespace TopBetta\Frontend\Controllers;

use BaseController;
use Input;

use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\Response\ApiResponse;

class UserRegistrationController extends BaseController {

	protected $accountService;
	protected $response;

	function __construct(UserAccountService $accountService,
						 ApiResponse $response)
	{
		$this->accountService = $accountService;
		$this->response = $response;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try{
			$accountCreationResponse =  $this->accountService->createTopbettaUserAccount(Input::json()->all());
			return $this->response->success($accountCreationResponse);
		}catch(ValidationException $e){
			return $this->response->failed($e->getErrors(), 500, 500, 'User Registration Failed', 'User Registration Failed');
		}

	}

}
