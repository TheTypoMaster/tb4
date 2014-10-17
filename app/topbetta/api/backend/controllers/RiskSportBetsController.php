<?php namespace TopBetta\backend;

use TopBetta\Bet;
use Illuminate\Support\Facades\Input;
use TopBetta\Facades\BetRepo;

class RiskSportBetsController extends \BaseController {

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
     * Using store method for cancel/refund bet
     * Risk was having trouble sending DELETE request :(
	 *
	 * @return Response
	 */
	public function store()
	{
        $input = Input::all();
        if (!$input) {
            $input = Input::json()->all();
        }

        $bet = Bet::findOrFail($input['id']);
        $cancel = ($input['action'] == 'cancel') ? true : false;

        if (!BetRepo::refundBet($bet, $cancel)) {
            return array("success" => false, "error" => "Problem with " . $input['action'] . " for sport bet id: " . $bet->id);
        }

        return array("success" => true, "result" => "Sport bet " . $input['action'] . ' successful for id: ' . $bet->id);
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
