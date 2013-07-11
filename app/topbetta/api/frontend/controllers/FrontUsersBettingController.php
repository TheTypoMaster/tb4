<?php
namespace TopBetta\frontend;

use TopBetta;

class FrontUsersBettingController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth');
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$limit = \Input::get('per_page', 25);
		$page = \Input::get('page', 1);
		
		$offset = $limit * ($page - 1);	
			
		$filter = array(
			'user_id'		=> \Auth::user() -> id,
			'result_type'	=> \Input::get('type', false)
			//'from_time'		=> $filter_from_date ? strtotime($filter_from_date) : null,
			//'to_time'		=> $filter_to_date ? (strtotime($filter_to_date) + 24 * 60 * 60) : null,
		);			
		
		$betModel = new \TopBetta\Bet;
		
		return $betModel->getBetFilterList($filter, 'b.id DESC', 'ASC', $limit, $offset);
			
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