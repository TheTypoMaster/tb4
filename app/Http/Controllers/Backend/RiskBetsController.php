<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Models\BetModel;
use Illuminate\Support\Facades\Input;
use TopBetta\Facades\BetRepo;

class RiskBetsController extends Controller {

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
		//
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
        $input = Input::all();
        if (!$input) {
            $input = Input::json()->all();
        }

        $bet = BetModel::findOrFail($input['id']);
        $cancel = ($input['action'] == 'cancel') ? true : false;

        if (!BetRepo::refundBet($bet, $cancel)) {
            return array("success" => false, "error" => "Problem with " . $input['action'] . " for bet id: " . $bet->id);
        }

        return array("success" => true, "result" => "Bet " . $input['action'] . ' successful for id: ' . $bet->id);
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
