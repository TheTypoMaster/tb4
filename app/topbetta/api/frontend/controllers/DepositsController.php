<?php

namespace TopBetta\Frontend;

use Input;
use Auth;
use TopBetta\Services\Accounting\DepositService;
use TopBetta\Services\Accounting\Payments\Exceptions\PaymentException;
use TopBetta\Services\Response\ApiResponse;

class DepositsController extends \BaseController {

    /**
     * @var DepositService
     */
    private $depositService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(DepositService $depositService, ApiResponse $response)
    {
        $this->depositService = $depositService;
        $this->response = $response;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		$user = Auth::user();

        $input = Input::json()->all();

        try {
            $this->depositService->creditCardDeposit($user, $input);
        } catch ( PaymentException $e ) {
            return $this->response->failed($e->getMessage());
        }

        return $this->reponse->success("Successful deposit");
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
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
