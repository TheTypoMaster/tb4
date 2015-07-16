<?php

namespace TopBetta\Http\Controllers\Frontend;

use Input;
use Auth;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Betting\Exceptions\BetPlacementException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Betting\Factories\BetPlacementFactory;
use TopBetta\Services\Response\ApiResponse;

class BetController extends Controller {

    /**
     * @var ApiResponse
     */
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
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
		$input = Input::json()->all();

        try {
            $service = BetPlacementFactory::make(array_get($input, 'bet_type', ''));
            $response = $service->placeBet(Auth::user(), $input['amount'], $input['bet_type'], $input['origin'], $input['selections'], $input['free_credit_flag']);
        } catch ( BetSelectionException $e ) {
            $selection = $e->getSelection();
            return $this->apiResponse->failed(array($e->getMessage(), $selection ? $selection->id : null));
        } catch ( BetLimitExceededException $e ) {
            return $this->apiResponse->failed(array($e->getMessage()));
        } catch ( BetPlacementException $e ) {
            return $this->apiResponse->failed(array($e->getMessage()));
        } catch ( \Exception $e ) {
            return $this->apiResponse->failed(array($e->getMessage()));
        }

        return $this->apiResponse->success($response);
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
