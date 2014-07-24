<?php

namespace TopBetta\admin\controllers;

use BaseController;
use Request;
use TopBetta\Repositories\WithdrawalsRepo;
use View;

class WithdrawalsController extends BaseController
{

	/**
	 * @var WithdrawalsRepo
	 */
	private $withdrawalRepo;

	public function __construct(WithdrawalsRepo $withdrawalRepo)
	{
		
		$this->withdrawalRepo = $withdrawalRepo;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');
		if ($search) {
			$withdrawals = $this->withdrawalRepo->search($search);
		} else {
			$withdrawals = $this->withdrawalRepo->allWithdrawals();
		}

		return View::make('admin::withdrawals.index', compact('withdrawals', 'search'));
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
