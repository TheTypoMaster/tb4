<?php
namespace TopBetta\frontend;

use Input;
use TopBetta\Services\Accounting\PoliTransactionService;
use TopBetta\Services\Response\ApiResponse;

class FrontUsersPoliDepositController extends \BaseController {

	/**
	 * @var PoliService
	 */
	private $poliService;
	/**
	 * @var ApiResponse
	 */
	private $response;

	public function __construct(PoliTransactionService $poliService, ApiResponse $response){
		$this->poliService = $poliService;
		$this->response = $response;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id = false)
	{
		$data = Input::get();

		$response = $this->poliService->initiateTransaction($data);
		$responseArray = $response->json();

		if($responseArray['Success']){
			return $this->response->success($responseArray);
		}

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
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $token)
	{
		$token = urldecode('rXMidznVQqPSC%2FB697yiJQyVDg1aORlo');

		dd($this->poliService->getTransactionDetails($token)->json());

		return;
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


}
