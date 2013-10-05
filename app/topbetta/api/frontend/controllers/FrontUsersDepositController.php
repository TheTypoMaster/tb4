<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontUsersDepositController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		if (!\Auth::user() -> isTopBetta) {

			return array("success" => false, "error" => \Lang::get('users.needs_upgrade'));

		}

		$type = \Input::get('type', null);

		if (!$type) {

			return array("success" => false, "error" => \Lang::get('banking.invalid_type'));

		}

		switch ($type) {

			case 'bankdeposit' :
				return array("success" => true, "result" => array('bank' => 'Commonwealth Bank of Australia', 'BSB' => '062-950', 'account' => '10080711', 'logo' => url('images/bank_logos/logo_cba.jpg'), 'message' => "Please ensure your username (" . \Auth::user() -> username . ") or TopBetta ID number (" . \Auth::user() -> id . ") is quoted on deposit slip."));
				break;

			case 'bpay' :
				$numDigits = 7;
				$userPin = sprintf("%0" . $numDigits . "d", \Auth::user() -> id);
				$bpayRef = $userPin . $this -> mod10($userPin);
				$billerCode = '135194';

				return array("success" => true, "result" => array('title' => 'Telephone & Internet Banking - BPAY&reg;', 'biller_code' => $billerCode, 'ref' => $bpayRef, 'logo' => url('images/bank_logos/logo_bpay_55.gif'), 'message' => 'Contact your bank, credit union or building society to make this payment from your cheque, savings, debit or credit card account. More info: www.bpay.com.au'));
				break;

			default :
				return array("success" => false, "error" => \Lang::get('banking.invalid_type'));
				break;
		}
	}

	/**
	 * Calculate check digit for bPay reference
	 *
	 * @return int
	 */
	private function mod10($seedval) {
		$mysum = 0;
		for ($x = 0; $x < strlen($seedval); $x++) {
			$digit = substr($seedval, $x, 1);
			if (strlen($seedval) % 2 == 1) {
				//to multiply by 2 and then by 1
				if ($x / 2 == floor($x / 2))
					$digit *= 2;
				// end multiplicaton
			} else {
				//to multiple first by 1 and then by 2
				if ($x / 2 == floor($x / 2)) {
					$digit *= 1;
				} else {
					$digit *= 2;
				} //end multiplication
			}
			if (strlen($digit) == 2)
				$digit = substr($digit, 0, 1) + substr($digit, 1, 1);
			$mysum += $digit;
		}
		$rem = $mysum % 10;
		//if remainder is string, just a way to convert to integer by adding 0
		$rem = $rem + 0;
		if ($rem == 0) {
			$checkdigit = 0;
		} else {
			$checkdigit = 10 - $rem;
		}
		return $checkdigit;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {

		if (!\Auth::user() -> isTopBetta) {

			return array("success" => false, "error" => \Lang::get('users.needs_upgrade'));

		}

		$type = \Input::get('type', null);

		if (!$type) {

			return array("success" => false, "error" => \Lang::get('banking.invalid_type'));

		}

		switch ($type) {
			case 'creditcard' :
				return $this -> doCreditCardDeposit();
				break;

			case 'moneybookers' :
				return array("success" => false, "error" => 'Not implemented yet');
				//return $this -> doMoneyBookersDeposit();
				break;
			
			case 'tokencreditcard' :
				$action = \Input::get('action', null);
				if (!$action) {
					return array("success" => false, "error" => \Lang::get('banking.missing_action'));
				}
				
				return $this->ewayTokenCreditCard($action);
				break;

			default :
				return array("success" => false, "error" => \Lang::get('banking.invalid_type'));
				break;
		}
	}

	/**
	 * Credit card deposit via legacy api
	 *
	 * @return array
	 */
	private function doCreditCardDeposit() {

		//validate our data requirements are met
		$input = Input::json() -> all();
		$year = date('y');

		$rules = array('name' => 'required|min:3', 'card_number' => 'required|min:13|max:19', 'expiry_month' => 'required|max:12', 'expiry_year' => "required|size:2", 'cvc' => 'required|min:3', 'amount' => 'required|numeric');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

            //temp fix
            if ($input['card_type'] == 1) {
                    $input['card_type'] = 'visa';
            }
            if ($input['card_type'] == 2)  {
                    $input['card_type'] = 'mastercard';
            }

			//pass data onto legacy api
			$l = new \TopBetta\LegacyApiHelper;
			$deposit = $l -> query('doInstantDeposit', $input);

			if ($deposit['status'] == 200) {

				return array("success" => true, "result" => $deposit['msg']);

			} else {

				return array("success" => false, "error" => $deposit['error_msg']);

			}

		}

	}
	
	
	/**
	 * Credit card deposit via E-Way //TODO: Move most of the stuff here to a library
	 *
	 * @return array
	 */
	private function ewayTokenCreditCard($action) {
	
		// grag all the input variables from the POST
		$input = Input::json()->all();
		
		// validate input and build SOAP request body
		switch ($action){
			
			 /* 
			  * This is called the 1st time a client makes a CC eway payment. It creates an eway token for the CC,
			  * stores the token in our DB and processes a payment with the stored CC token	
			  */
			case 'create_customer_and_payment':
				
				/* FULL validation if we require it later
				 * $rules = array('Title' => 'required|In:Mr,Ms,Mrs,Miss,Dr,Sir,Prof', 'FirstName' => 'required|between:2,50', 
						'LastName' => 'required|between:2,50', 'Address' => "max:255", 'Suburb' => 'max:50', 'State' => 'max:50', 
						'Company' => 'max:100', 'PostCode' => 'between:4,6', 'Country' => 'required|max:2', 'Email' => 'email|max:50',
						'Fax' => 'max:20', 'Phone' => 'max:20', 'CustomerRef' => 'max:20', 'JobDesc' => 'max:50', 
						'Comments' => 'max:255', 'URL' => 'url|max:255', 'CCNumber' => 'required|max:20', 'CCNameOnCard' => 'max:50',
						'CCExpiryMonth' => 'required|size:2', 'CCExpiryYear' => 'required|size:2' );
				 */
								
				
				// Get required user details from the database. Title/First and Last name, Country
				$topbettaUserDetails = TopBetta\TopBettaUser::where('user_id', '=', \Auth::user()->id)->get()->toArray();
				$title = $topbettaUserDetails[0]['title'];
				$firstName = $topbettaUserDetails[0]['first_name'];
				$lastName = $topbettaUserDetails[0]['last_name'];
				$country = $topbettaUserDetails[0]['country'];
			
				// Validate the data required to make a new customer and initial deposit is correct
				$rules = array('CCNumber' => 'required|max:20', 'CCNameOnCard' => 'max:50',
							'CCExpiryMonth' => 'required|size:2', 'CCExpiryYear' => 'required|size:2', 'amount' => 'required|Integer' );
				$validated = $this->validateInput($input, $rules);
				
				// If there are no validation error
				if(!$validated['error']){
					// Add the required data to the array for the SOAP request body
					$createCustomerArray = array('Title' => $title, 'FirstName' => $firstname, 'LastName' => $lastname, 'Country' => $country,
							'CCNumber' => $input['CCNumber'], 'CCExpiryMonth' => $input['CCExpiryMonth'], 'CCExpiryYear' => $input['CCExpiryYear']);
		
					// Make the SOAP call
					$soapResponse = $this->ewayProcessRequest($createCustomerArray, 'CreateCustomer');
					
					// check we got a new managedCustomerID
					if(!$soapResponse['error'] && $soapResponse['message']->CreateCustomerResult){
						
						// Store the new CC token in our DB
						$ccTokenModel = new TopBetta\PaymentEwayTokens();
						$ccTokenModel->user_id = \Auth::user()->id;
						$ccTokenModel->cc_token = $soapResponse['message']->CreateCustomerResult;
						$ccTokenModel->save();
						
						// Process a payment with the new managedCustomerID
						$paymentArray = array('managedCustomerID' => $soapResponse['message']->CreateCustomerResult, 'amount' => $input['amount'], 
											'invoiceReference' => \Auth::user()->id, 'invoiceDescription' => 'TopBetta Deposit');
						$soapResponse = $this->ewayProcessRequest($paymentArray, 'ProcessPayment');
						
						// Check if CC payment was processed successfully
						if(!$soapResponse['error'] && $soapResponse['message']->ewayTrxnStatus = 'True'){
							// Update users account balance
							$updateAccountBalance = TopBetta\AccountBalance::_increment(\Auth::user()->id, $soapResponse['message']->ewayReturnAmount, 'ewaydeposit', 'EWAY transaction id:'.$ewayTransactionNumber.' - Bank authorisation number:'.$ewayAuthCode);
							
							if($updateAccountBalance){
								return array("error" => false, "message" => \Lang::get('banking.cc_payment_success'));
							}else{
								//TODO: If updating of account balance fails then let someone know!?!?
								return array("error" => true, "message" => \Lang::get('banking.cc_payment_accbal_update_failed'));
							}
						}else{
							// return failed message
							return array("error" => true, "message" => \Lang::get('banking.cc_payment_failed'));
						}
					}else{
						return array("error" => true, "message" => \Lang::get('banking.customer_creation_failed'));
					}
						
				}else{
					return array("error" => true, "message" => 'Validation Failed :'.$validated['message']);
				}
				break;
				
			case 'create_customer':
				$rules = array('Title' => 'required|In:Mr,Ms,Mrs,Miss,Dr,Sir,Prof', 'FirstName' => 'required|between:2,50',
							 'LastName' => 'required|between:2,50', 'Address' => "max:255", 'Suburb' => 'max:50', 'State' => 'max:50',
							 'Company' => 'max:100', 'PostCode' => 'between:4,6', 'Country' => 'required|max:2', 'Email' => 'email|max:50',
							 'Fax' => 'max:20', 'Phone' => 'max:20', 'CustomerRef' => 'max:20', 'JobDesc' => 'max:50',
							 'Comments' => 'max:255', 'URL' => 'url|max:255', 'CCNumber' => 'required|max:20', 'CCNameOnCard' => 'max:50',
							 'CCExpiryMonth' => 'required|size:2', 'CCExpiryYear' => 'required|size:2' );

				$validated = $this->validateInput($input, $rules);
			
				if($validated['error'] == false){
					$requestbody =  $this->buildRequestBody($input);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'CreateCustomer');
				}
			
				break;

			case 'update_customer':
				$rules = array('Title' => 'required|In:Mr,Ms,Mrs,Miss,Dr,Sir,Prof', 'FirstName' => 'required|between:2,50',
						'LastName' => 'required|between:2,50', 'Address' => "max:255", 'Suburb' => 'max:50', 'State' => 'max:50',
						'Company' => 'max:100', 'PostCode' => 'between:4,6', 'Country' => 'required|max:2', 'Email' => 'email|max:50',
						'Fax' => 'max:20', 'Phone' => 'max:20', 'CustomerRef' => 'max:20', 'JobDesc' => 'max:50',
						'Comments' => 'max:255', 'URL' => 'url|max:255', 'CCNumber' => 'required|max:20', 'CCNameOnCard' => 'max:50',
						'CCExpiryMonth' => 'required|size:2', 'CCExpiryYear' => 'required|size:2' );
				$validated = $this->validateInput($input, $rules);
				
				if($validated['error'] == false){
					$requestbody =  $this->buildRequestBody($input);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'UpdateCustomer');
				}
				break;
				
			case 'process_payment':
				$rules = array('managedCustomerID' => 'required', 'amount' => 'required|Integer', 'invoiceReference' => 'required', 'invoiceDescription' => 'required');
				$validated = $this->validateInput($input, $rules);
				
				if($validated['error'] == false){
					$requestbody =  $this->buildRequestBody($input);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'ProcessPayment');
				}
				break;
				
			case 'process_payment_with_cvn':
				$rules = array('managedCustomerID' => 'required', 'amount' => 'required|Integer', 'invoiceReference' => 'required', 'invoiceDescription' => 'required', 'CVN' => 'required|between:2,4');
				$validated = $this->validateInput($input, $rules);
				
				if($validated['error'] == false){
					$requestbody =  $this->buildRequestBody($input);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'ProcessPaymentWithCVN');
				}
				break;

			case 'query_payment':
				$rules = array('managedCustomerID' => 'required');
				$validated = $this->validateInput($input, $rules);
				
				if($validated['error'] == false){
					$requestbody =  $this->buildRequestBody($input);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'QueryPayment');
				}
				break;
				
			case 'query_customer':
				
				
				//$rules = array('managedCustomerID' => 'required');
				//$validated = $this->validateInput($input, $rules);
				
			
				
				$ccTokenDetailsArray = array();
				
				
				// grab all the managedCustomerId's for the users stored CC's out of the database
				$usersCCTokens = TopBetta\PaymentEwayTokens::where('user_id', '=', \Auth::user()->id)->get();
				
				foreach($usersCCTokens as $userToken){
					$requestbody =  $this->buildRequestBody($userToken->cc_token);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'QueryCustomer');
					
					if(!$soapResponse['error']){
						$ccTokenDetailsArray[] = $soapResponse['message'];
					}
					
					
				}
				
					
				
				break;
				
			case 'query_customer_ref':
				$rules = array('CustomerReference' => 'required|max:20');
				$validated = $this->validateInput($input, $rules);
				
				if(!$validated['error']){
					$requestbody =  $this->buildRequestBody($input);
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'QueryCustomerByReference');
				}
				break;
				
			default :
				return array("error" => true, "message" => \Lang::get('banking.invalid_action'));
				break;
		}
		
		if($validated['error']){
			return array("error" => true, "message" => 'Validation Failed :'.$validated['message']);
		}
		
		
		
		return $soapResponse;
	}
	
	
	private function validateInput($input, $rules){
		$validator = \Validator::make($input, $rules);
		if ($validator -> fails()) {
			return array("error" => true, "message" => $validator->messages()->toJSON());
			//return false; 
		} else {
			return array("error" => false, "message" => "Validation Passed");
		}
	}
	
	
	function buildRequestBody($input) {
		$requestBodyArray = array();
	
		foreach($input as $key=>$value){
	   	 if(!empty($value)) $requestBodyArray[$key] = $value;
	  }
		return $requestBodyArray;
	}
	
	
	/**
	 * Send the CC request to the E-WAY SOAP endpoint
	 *
	 * @return array
	 */
	
	private function ewayProcessRequest($requestbody, $method){
		
		
		// init soap client
		$soapClient = new \SoapClient('https://www.eway.com.au/gateway/ManagedPaymentService/test/managedCreditCardPayment.asmx?WSDL', array('trace' => 1));
		
		// TODO: move to laravel config file
		$sh_param = array(
				'eWAYCustomerID' => '87654321',
				'Username' => 'test@eway.com.au',
				'Password' => 'test123'	);
		
		// set the SOAP headers
		$headers = new \SoapHeader('https://www.eway.com.au/gateway/managedpayment', 'eWAYHeader', $sh_param);
		
		// Prepare Soap Client
		$soapClient->__setSoapHeaders(array($headers));
		
		//make the call
		try {
			$soapCall = $soapClient->$method($requestbody,$headers);
		} catch (SoapFault $fault) {
			return array("error" => true, "message" => $fault->faultcode." Ð ".$fault->faultstring);
		}

		//return $soapCall;
		
		// return response from soap request
		return array("error" => false, "message" => $soapCall);
	}
	

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
	}

}
