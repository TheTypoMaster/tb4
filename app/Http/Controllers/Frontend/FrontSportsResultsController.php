<?php namespace TopBetta\Http\Frontend\Controllers;

use TopBetta;

class FrontSportsResultsController extends \BaseController {

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

		// store event results in cache for 2 min at a time
		return \Cache::remember('sportsResults-' . $id, 2, function() use($id) {

			$eventResults = \TopBetta\SportsResults::getResultsForEventId($id);

			$result = array();

			foreach ($eventResults as $market) {


				$result[] = array('id' => (int)$market -> id, 'market' => $market -> market, 'result' => $market -> name, 'option_id' => (int)$market -> selection_id);
			}

			return array('success' => true, 'result' => $result);

		});
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