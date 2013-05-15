<?php
namespace TopBetta\backend;

use TopBetta;

class BetResultsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
//	public function index()
//	{
		//
//		return "In index function";
//	}

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
		$betObject = Bet::where('invoice_id', '=', $id)->get();
		return $betObject->toArray();
		
		//return "In show Function";
		
		
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
		//TODO: Update existing bet records
		// Get bet details. Need to cater for free and real bet amounts
		
		
		
		
		
		
		$betObject = Bet::where('invoice_id', '=', $id)->get();
		
		
		
		return "Record updated";
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
	
	
	// Increment users balance
	
	// Decriment users balance
	
	

}