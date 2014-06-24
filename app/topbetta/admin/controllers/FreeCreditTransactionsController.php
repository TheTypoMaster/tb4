<?php

namespace TopBetta\admin\controllers;

use BaseController;
use TopBetta\Repositories\FreeCreditTransactionRepo;
use View;

class FreeCreditTransactionsController extends BaseController
{

	/**
	 * @var FreeCreditTransactionRepo
	 */
	private $freeCreditTransactionRepo;

	public function __construct(FreeCreditTransactionRepo $freeCreditTransactionRepo)
	{

		$this->freeCreditTransactionRepo = $freeCreditTransactionRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$transactions = $this->freeCreditTransactionRepo->allTransactions();

		return View::make('admin::transactions.index')
						->with(compact('transactions'))
						->with('title', 'Free Credit');
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
