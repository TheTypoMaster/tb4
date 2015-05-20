<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Repositories\AccountTransactionRepo;
use View;

class AccountTransactionsController extends Controller {

	/**
	 * @var AccountTransactionRepo
	 */
	private $accountTransactionRepo;

	public function __construct(AccountTransactionRepo $accountTransactionRepo)
	 {
		 
		 $this->accountTransactionRepo = $accountTransactionRepo;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$transactions = $this->accountTransactionRepo->allTransactions();
		
		return View::make('admin.transactions.index')
				->with(compact('transactions'))
				->with('title', 'Account');
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