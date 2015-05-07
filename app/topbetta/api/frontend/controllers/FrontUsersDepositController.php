<?php
namespace TopBetta\frontend;

use TopBetta;
use Mail;
use Auth;
use Illuminate\Support\Facades\Input;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;

class FrontUsersDepositController extends \BaseController {

	private $depositTypeMapping = array(
		"tokencreditcard" => "Eway",
	);
    /**
     * @var UserAccountService
     */
    private $userAccountService;

    /**
     * @var UserDashboardNotificationService
     */
    private $dashboardNotificationService;

    public function __construct(UserAccountService $userAccountService, UserDashboardNotificationService $dashboardNotificationService) {
		$this -> beforeFilter('auth');
        $this->userAccountService = $userAccountService;
        $this->dashboardNotificationService = $dashboardNotificationService;
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
			
			case 'query_eway_customer':

				$ccTokenDetailsArray = array();

				// grab all the managedCustomerId's for the users stored CC's out of the database
				$usersCCTokens = TopBetta\PaymentEwayTokens::getEwayTokens(\Auth::user()->id);
			
				$tokenCount = count($usersCCTokens);
			
				if(count($usersCCTokens)){
					// loop through each token found and query E-Way for the CC details
					foreach($usersCCTokens as $userToken){
						$requestbody = array('managedCustomerID' => $userToken->cc_token);
						// make the SOAP call
						$soapResponse = $this->ewayProcessRequest($requestbody, 'QueryCustomer');
			
						if($soapResponse['success']){
							$ccTokenDetailsArray[] = $soapResponse['result']->QueryCustomerResult;
						}
					}
					return array('success' => true, 'result' => $ccTokenDetailsArray);
				}else{
					return array('success' => false, 'error' => 'No Stored Tokens');
				}
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
				
				$result = $this->ewayTokenCreditCard($action);

				break;

			default :
				return array("success" => false, "error" => \Lang::get('banking.invalid_type'));
				break;
		}

		//Send email to help@topbetta.com if a promo code is present
		if($result['success'] && $promoCode = \Input::json("promo_code", false)) {
			try {

				$this->sendPromoCodeEmail($promoCode, \Input::json("amount"), $type);

				//success so update result
				$result['result'] .= \Lang::get("banking.promo_code_deposit");

			} catch (\Exception $e) {
				//error sending email
				\Log::error("Error sending promo code information with message " . $e->getMessage() . ", User " . \Auth::user()->id . " Promo code " . $promoCode . " Deposit Amount " . \Input::json("amount"));

				$result['result'] .= \Lang::get("banking.promo_code_deposit_error");
			}
		}

		return $result;
	}

	/**
	 * Send promo code email
	 * @param $promoCode
	 * @param $amount
	 * @param $method
	 */
	public function sendPromoCodeEmail($promoCode, $amount, $method)
	{
		$user = \Auth::user();

		Mail::send(
			'emails.promo_code',
			array(
				"user"          => $user,
				"amount"        => $amount,
				"paymentMethod" => $this->depositTypeMapping[$method],
				"promoCode"     => $promoCode
			),
			function($message) use ($user) {
				$message
					->to(\Config::get('mail.promo_code_to.address'), \Config::get('mail.promo_code_to.name'))
					->subject("Promo Code for User " . $user->id);
			}
		);
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

		// Grab all the input variables from the POST
		$input = Input::json()->all();
				
		// Validate input and build SOAP request body
		switch ($action){
			
			 /* 
			  * This is called the 1st time a client makes a CC eway payment. It creates an eway token for the CC,
			  * stores the token in our DB and processes a payment with the stored CC token	
			  */
			case 'create_customer_and_payment':
			
				// Get required user details from the database. Title/First and Last name, Country
				$topbettaUserDetails = TopBetta\TopBettaUser::getTopBettaUserDetails(\Auth::user()->id)->toArray();
				$title = $topbettaUserDetails[0]['title'];
				$firstName = $topbettaUserDetails[0]['first_name'];
				$lastName = $topbettaUserDetails[0]['last_name'];
				$country = strtolower($topbettaUserDetails[0]['country']);
				$address = $topbettaUserDetails[0]['street'];
				$suburb = $topbettaUserDetails[0]['city'];
				$state = strtoupper($topbettaUserDetails[0]['state']);
				$postcode = $topbettaUserDetails[0]['postcode'];
			
				// Validate the data required to make a new customer and initial deposit is correct
				$rules = array('CCNumber' => 'required|max:20', 'CCName' => 'max:50',
							'CCExpiryMonth' => 'required|size:2', 'CCExpiryYear' => 'required|size:2', 'amount' => 'required|Integer|Min:1000' );

				$validationMessages = array(
					"CCNumber"      => array(
						"required" => "The card number is required",
						"max"      => "Card number cannot be more than 20 characters",
					),
					"CCName"        => array(
						"required" => "The card name is required",
						"max"      => "Card name cannot be more than 50 characters",
					),
					"CCExpiryMonth" => array(
						"required" => "The card expiry month is required",
						"size"     => "Card expiry month must be 2 characters"
					),
					"CCExpiryYear"  => array(
						"required" => "The card expiry year is required",
						"size"     => "Card expiry year must be 2 characters"
					),
					"Amount"        => array(
						"required" => "Amount is required",
						"min"      => "The deposit amount must be at least $10"
					)
				);
				
				$validator = \Validator::make($input, $rules, $validationMessages);
				if ($validator -> fails()) {				
					return array("success" => false, "error" => $validator -> messages() -> all());
				} else {
				
					($title == 'Miss') ? $title = $title : $title = $title.'.';
					
					// Add the required data to the array for the SOAP request body
					$createCustomerArray = array('Title' => $title, 'FirstName' => $firstName, 'LastName' => $lastName, 'Country' => $country,
							'CCNumber' => $input['CCNumber'], 'CCNameOnCard' => $input['CCName'], 'CCExpiryMonth' => $input['CCExpiryMonth'], 'CCExpiryYear' => $input['CCExpiryYear'],
							'Address' => $address, 'Suburb' => $suburb, 'State' => $state, 'Company' => '', 'PostCode' => $postcode, 'Email' => '', 'Fax' => '', 'Phone' => '',
							'Mobile' => '', 'CustomerRef' => '', 'JobDesc' => '', 'Comments' => '', 'URL' => '');
		
					// Make the SOAP call
					$soapResponse = $this->ewayProcessRequest($createCustomerArray, 'CreateCustomer');
					
					// check we got a new managedCustomerID
					if($soapResponse['success'] && $soapResponse['result']->CreateCustomerResult){
						
						// Store the new CC token in our DB
						$ccTokenModel = new TopBetta\PaymentEwayTokens();
						$ccTokenModel->user_id = \Auth::user()->id;
						$ccTokenModel->cc_token = $soapResponse['result']->CreateCustomerResult;
						$ccTokenModel->save();
						
						// Process a payment with the new managedCustomerID
						$paymentArray = array('managedCustomerID' => $soapResponse['result']->CreateCustomerResult, 'amount' => $input['amount'], 
											'invoiceReference' => \Auth::user()->id, 'invoiceDescription' => 'TopBetta Deposit');
						$soapResponse = $this->ewayProcessRequest($paymentArray, 'ProcessPayment');
						
						// Check if CC payment was processed successfully
						if($soapResponse['success'] && $soapResponse['result']->ewayResponse->ewayTrxnStatus == 'True'){

							// Update users account balance
							$updateAccountBalance = TopBetta\AccountBalance::_increment(\Auth::user()->id, $soapResponse['result']->ewayResponse->ewayReturnAmount, 'ewaydeposit', 'EWAY transaction id:'.$soapResponse['result']->ewayResponse->ewayTrxnNumber.' - Bank authorisation number:'.$soapResponse['result']->ewayResponse->ewayAuthCode);
							
							if($updateAccountBalance){
                                $this->userAccountService->addBalanceToTurnOver(\Auth::user()->id, $soapResponse['result']->ewayResponse->ewayReturnAmount);
                                $this->dashboardNotificationService->notify(array("id" => \Auth::user()->id, "transactions" => array($updateAccountBalance)));
                                return array("success" => true, "result" => \Lang::get('banking.cc_payment_success'));
							}else{
								//TODO: If updating of account balance fails then let someone know!?!?
								return array("success" => false, "result" => \Lang::get('banking.cc_payment_accbal_update_failed'));
							}
						}else{
							// return failed message
							return array("success" => false, "error" => \Lang::get('banking.cc_payment_failed'));
						}
					}else{
						return array("success" => false, "error" => \Lang::get('banking.customer_creation_failed'));
					}
						
				}
				break;
				
		
			case 'process_payment':
				
				$validationMessages = array('amount.min' => 'The deposit amount must be at least $10');
				$rules = array('managedCustomerID' => 'required', 'amount' => 'required|Integer|Min:1000');
				$validator = \Validator::make($input, $rules, $validationMessages);
				if ($validator -> fails()) {				
					return array("success" => false, "error" => $validator -> messages() -> all());
				} else {
					
					// Check the managed customer ID is stored in the DB
					$usersCCTokenID = TopBetta\PaymentEwayTokens::checkTokenExists(\Auth::user()->id, $input['managedCustomerID']);
					
					if (!$usersCCTokenID){
						return array("success" => false, "error" => \Lang::get('banking.cc_token_invalid'));
					}
					// add invoice ref an invoice decription to the request body
					$invoiceDetail = array('InvoiceReference' => \Auth::user()->id, 'InvoiceDescription' => 'TopBetta Deposit');
					$input = array_merge($input, $invoiceDetail);
				
					// Build the SOAP request body
					$requestbody =  $this->buildRequestBody($input);
					
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'ProcessPayment');
					
					// Check if CC payment was processed successfully
					if($soapResponse['success'] && $soapResponse['result']->ewayResponse->ewayTrxnStatus == 'True'){
					
						// Update users account balance
						$updateAccountBalance = TopBetta\AccountBalance::_increment(\Auth::user()->id, $soapResponse['result']->ewayResponse->ewayReturnAmount, 'ewaydeposit', 'EWAY transaction id:'.$soapResponse['result']->ewayResponse->ewayTrxnNumber.' - Bank authorisation number:'.$soapResponse['result']->ewayResponse->ewayAuthCode);

						if($updateAccountBalance){
                            $this->userAccountService->addBalanceToTurnOver(\Auth::user()->id, $soapResponse['result']->ewayResponse->ewayReturnAmount);
                            $this->dashboardNotificationService->notify(array("id" => \Auth::user()->id, "transactions" => array($updateAccountBalance)));
                            return array("success" => true, "result" => \Lang::get('banking.cc_payment_success'));
						}else{
							//TODO: If updating of account balance fails then let someone know!?!?
							return array("success" => false, "error" => \Lang::get('banking.cc_payment_accbal_update_failed'));
						}
					}else{
						// return failed message
						return array("success" => false, "error" => \Lang::get('banking.cc_payment_failed'));
					}
				}
				break;
				
			case 'process_payment_with_cvn':
				
				$rules = array('managedCustomerID' => 'required', 'amount' => 'required|Integer', 'CVN' => 'required|between:2,4');
				$validator = \Validator::make($input, $rules);
				if ($validator -> fails()) {				
					return array("success" => false, "error" => $validator -> messages() -> all());
				} else {
					// add invoice ref an invoice decription to the request body
					$invoiceDetail = array('InvoiceReference' => \Auth::user()->id, 'InvoiceDescription' => 'TopBetta Deposit');
					$input = array_merge($input, $invoiceDetail);
					
					// Build the SOAP request body
					$requestbody =  $this->buildRequestBody($input);
					
					// make the SOAP call
					$soapResponse = $this->ewayProcessRequest($requestbody, 'ProcessPaymentWithCVN');
					
					// Check if CC payment was processed successfully
					if($soapResponse['success'] && $soapResponse['result']->ewayResponse->ewayTrxnStatus = 'True'){
							
						// Update users account balance
						$updateAccountBalance = TopBetta\AccountBalance::_increment(\Auth::user()->id, $soapResponse['result']->ewayResponse->ewayReturnAmount, 'ewaydeposit', 'EWAY transaction id:'.$soapResponse['result']->ewayResponse->ewayTrxnNumber.' - Bank authorisation number:'.$soapResponse['result']->ewayResponse->ewayAuthCode);

						if($updateAccountBalance){
                            $this->userAccountService->addBalanceToTurnOver(\Auth::user()->id, $soapResponse['result']->ewayResponse->ewayReturnAmount);
                            $this->dashboardNotificationService->notify(array("id" => \Auth::user()->id, "transactions" => array($updateAccountBalance)));
							return array("success" => true, "result" => \Lang::get('banking.cc_payment_success'));
						}else{
							//TODO: If updating of account balance fails then let someone know!?!?
							return array("success" => false, "error" => \Lang::get('banking.cc_payment_accbal_update_failed'));
						}
					}else{
						// return failed message
						return array("success" => false, "error" => \Lang::get('banking.cc_payment_failed'));
					}
				}
				break;
				
			default :
				return array("success" => false, "error" => \Lang::get('banking.invalid_action'));
				break;
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
		
		
		// Grab config details
		$soapEndPoint = \Config::get('eway.soapEndPoint');
		$customerID = \Config::get('eway.eWAYCustomerID');
		$userName = \Config::get('eway.Username');
		$password = \Config::get('eway.Password');
		
		// init soap client
		$soapClient = new \SoapClient($soapEndPoint, array('trace' => 1));
		
		$sh_param = array(
				'eWAYCustomerID' => $customerID,
				'Username' => $userName,
				'Password' => $password	);
		
		// set the SOAP headers
		$headers = new \SoapHeader('https://www.eway.com.au/gateway/managedpayment', 'eWAYHeader', $sh_param);
		
		// Prepare Soap Client
		$soapClient->__setSoapHeaders(array($headers));
		
	 	//make the call
		try {
			$soapCall = $soapClient->$method($requestbody,$headers);
			\Log::info('EWAY SOAP RESPONSE:'.$soapClient->__getLastResponse());
		} catch (\SoapFault $fault) {
			\Log::error('EWAY SOAP ERROR - Code:'. $fault->faultcode. ', Message:'.$fault->faultstring);
			return array("success" => false, "error" => $fault->faultcode." � ".$fault->faultstring);
		} catch(\Exception $fault){
			\Log::error('EWAY SOAP ERROR - Code:'. $fault->faultcode. ', Message:'.$fault->faultstring);
			return array("success" => false, "error" => $fault->faultcode." � ".$fault->faultstring);
		}
	
		// return response from soap request
		return array("success" => true, "result" => $soapCall);
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
	public function destroy($id, $managedCustomerId) {

		$type = Input::get("type", null);

		//make sure a deposit type is specified
		if ( ! $type ) {

			return array("success" => false, "error" => \Lang::get('banking.invalid_type'));

		}

		//check we are deleting credit cards
		if($type == "query_eway_customer") {

			//check the specified credit card token exists
			if (TopBetta\PaymentEwayTokens::checkTokenExists(\Auth::user()->id, $managedCustomerId)) {
				//delete the token
				$paymentToken = TopBetta\PaymentEwayTokens::where("cc_token", "=", $managedCustomerId)->first();
				//dd($paymentToken->cc_token);
				$paymentToken->delete();

				return array('success' => true, 'result' => array());
			} else {
				return array('success' => false, 'error' => "Token not found");
			}

		} else {

			return array("success" => false, "error" => \Lang::get('banking.invalid_type'));
		}

	}

}
