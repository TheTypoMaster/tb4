<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use Input;
use Auth;
use Config;
use Redirect;
use TopBetta\Services\Accounting\Exceptions\TransactionNotFoundException;
use TopBetta\Services\Accounting\PoliApiService;
use TopBetta\Services\Accounting\PoliTransactionService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class FrontUsersPoliDepositController extends Controller {

	/**
	 * @var PoliService
	 */
	private $poliTransactionService;

	/**
	 * @var ApiResponse
	 */
	private $response;

	/**
	 * @var PoliApiService
	 */
	private $poliApiService;

	public function __construct(PoliTransactionService $poliTransactionService, PoliApiService $poliApiService,  ApiResponse $response){
		$this->poliTransactionService = $poliTransactionService;
		$this->response = $response;
		$this->poliApiService = $poliApiService;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id = false)
	{
		$data = \Input::all();
		$user = Auth::user();

		if(! ( $amount = array_get($data, 'Amount', 0) ) ) {
			return $this->response->failed(array(), 400, null, "No Amount Specified");
		}

		//create the transaction in the database
		$poliTransaction = $this->poliTransactionService->createTransaction($user->id, $amount, array_get($data, 'CurrencyCode', 'AUD'));

		if( ! $poliTransaction) {
			return $this->response->failed(array(), 500, null, "Unknown error occured when creating the transaction");
		}

		//initiate the transaction with POLI
		$response = $this->poliApiService->initiateTransaction($data, $poliTransaction['id'], $user->id);
		$responseArray = $response->json();

		//check response
		if( array_get($responseArray, 'Success', false) ) {
			//successful so initialize transaction in DB
			$this->poliTransactionService->initialize($poliTransaction['id'], $responseArray['TransactionRefNo']);
			return $this->response->success($responseArray);
		}

		//unsuccessful so init failed
		$this->poliTransactionService->initializationFailed($poliTransaction['id'], $responseArray['errorCode']);
		return $this->response->failed($responseArray, $response->getStatusCode());
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$token = \Input::get("token", null);

		return $this->_getTransactionDetailsAndUpdate($token);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $token)
	{
		$token = \Input::get("token", null);

		$response = $this->_getTransactionDetailsAndUpdate($token);

		if( $response ) {
			if(PoliTransactionService::statusIsTerminal($response['TransactionStatusCode'])) {
				return Redirect::to(Config::get("poli.frontendReturnUrl").$response['TransactionStatusCode']);
			}

			return Redirect::to(Config::get("poli.frontendReturnUrl")."pending");
		}

		return Redirect::to(Config::get("poli.frontendReturnUrl")."failed");
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	private function _getTransactionDetailsAndUpdate($token)
	{
		//get the transaction details from the Poli API
		$response = $this->poliApiService->getTransactionDetails($token);
		if( ! $response ) {
			return null;
		}

		//get the response body from Poli
		$responseArray = $response->json();

		//check the transaction reference is returned
		if( $transactionRefNo = array_get($responseArray, 'TransactionRefNo', false) ) {
			try {
				//update the transaction & add funds if completed.
				$poliTransaction = $this->poliTransactionService->updateTransactionTokenAndStatus(
					$transactionRefNo,
					$token,
					$responseArray['TransactionStatusCode'],
					$responseArray['ErrorCode']
				);
			} catch (TransactionNotFoundException $e) {

				return $responseArray;
			}

			return $responseArray;
		}

		return $responseArray;
	}



}
