<?php namespace TopBetta\Http\Controllers\Frontend;
/**
 * Coded by Oliver Shanahan
 * File creation date: 7/01/15
 * File creation time: 12:55
 * Project: tb4
 */

use TopBetta\Http\Controllers\Controller;

use Input;

use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class FrontAccountingController extends Controller {

    protected $accountservice;
    protected $response;

    function __construct(AccountTransactionService $accountservice,
                         ApiResponse $response)
    {
        $this->accountservice = $accountservice;
        $this->response = $response;
    }

    public function transferFunds(){
        $input = Input::json()->all();

        try {
            return $this->response->success($this->accountservice->transferFunds($input));

        }catch(ValidationException $e){
            return $this->response->failed($e->getErrors(), 200, 201, 'Funds not transferred', 'Funds not transaferred, refer to error object');

        }
    }

    public function returnFunds(){
        $input = Input::json()->all();

        try {
            return $this->response->success($this->accountservice->returnFunds($input));

        }catch(ValidationException $e){
            return $this->response->failed($e->getErrors(), 200, 201, 'Funds not transferred', 'Funds not transaferred, refer to error object');

        }
    }
}