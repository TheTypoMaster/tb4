<?php namespace TopBetta\Frontend;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 12:13
 * Project: tb4
 */

use BaseController;
use Exception;
use Input;

use TopBetta\Services\Authentication\TokenAuthenticationService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\Response\ApiResponse;

/**
 * Class FrontTokenController
 * @package TopBetta\Frontend
 */
class FrontTokenController extends BaseController{

    /**
     * @var TokenAuthenticationService
     */
    protected $tokenauth;
    /**
     * @var ApiResponse
     */
    protected $response;

    /**
     * Inject the required dependencies
     *
     * @param TokenAuthenticationService $tokenauth
     * @param ApiResponse $response
     */
    public function __construct(TokenAuthenticationService $tokenauth,
                                ApiResponse $response){
        $this->tokenauth = $tokenauth;
        $this->response = $response;
    }

    /**
     * This method will get the input from the POST and hand it off to the token request service.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokenRequest(){

        $input = Input::json()->all();

        try {
            $tokenRequest =  $this->tokenauth->processTokenRequest($input);
            return $this->response->success($tokenRequest);

        }catch(ValidationException $e){
            return $this->response->failed($e->getErrors(), 500, 500, 'No good', 'No good');

        }
    }

    public function tokenLogin(){
        $input = Input::all();

        try {
            $loginRequest = $this->tokenauth->tokenLogin($input);
            return $this->response->success($loginRequest);
        }catch(ValidationException $e){
            return $this->response->failed($e->getErrors(), 500, 500, 'Login Failed', 'User login with token failed');
        }
    }


    private function createChildAccount(){

    }


}