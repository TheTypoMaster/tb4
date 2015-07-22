<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Input;
use Auth;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Betting\BetService;
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
    /**
     * @var BetService
     */
    private $betService;

    public function __construct(ApiResponse $apiResponse, BetService $betService)
    {
        $this->apiResponse = $apiResponse;
        $this->betService = $betService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
	public function index(Request $request)
	{
        if( $date = $request->get('date', null) ) {
            return $this->apiResponse->success(
                $this->betService->getBetsForDate(Auth::user()->id, $date)->toArray()
            );
        }

		return $this->apiResponse->success(
            $this->betService->getBetHistory(Auth::user()->id, $request->get('type', 'all'), $request->get('page', null))->toArray()
        );
	}

    public function getActiveAndRecentBets()
    {
        $bets = $this->betService->getActiveAndRecentBetsForUser(Auth::user()->id);

        return $this->apiResponse->success(array(
            'active' => $bets['active']->toArray(),
            'recent' => $bets['recent']->toArray()
        ));
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
