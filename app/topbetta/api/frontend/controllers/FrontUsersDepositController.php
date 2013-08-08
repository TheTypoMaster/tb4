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
