<?php
namespace TopBetta\frontend;

use Illuminate\Support\Facades\Input;
use TopBetta\Services\Accounting\AccountTransactionService;

class FrontUsersWithdrawController extends \BaseController {

    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;

    public function __construct(AccountTransactionService $accountTransactionService) {
		$this -> beforeFilter('auth');
        $this->accountTransactionService = $accountTransactionService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//
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

		$type = Input::get('type', null);

		if (!in_array($type, array('bank', 'moneybookers', 'paypal'))) {

			return array("success" => false, "error" => \Lang::get('banking.invalid_type'));

		}

		//validate our data requirements are met
		$input = Input::json() -> all();

		$rules = array($type . '_amount' => 'required|numeric|min:20', $type . '_email' => 'required');

		//re-map our type for legacy
		$input['withdrawalType'] = $type;

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {
            \Log::info($input);
            //check available withdrawal funds
            if($input[$type.'_amount']*100 > $this->accountTransactionService->getAvailableWithdrawalBalance(\Auth::user()->id)) {
                return array("success" => false, "error" => "Withdrawal amount is greater than your available funds to withdraw");
            }

			//pass data onto legacy api
			$l = new \TopBetta\LegacyApiHelper;
			$withdraw = $l -> query('doWithdrawRequest', $input);

			if ($withdraw['status'] == 200) {

				return array("success" => true, "result" => $withdraw['msg']);

			} else {

				return array("success" => false, "error" => $withdraw['error_msg']);

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
