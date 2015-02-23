<?php

namespace TopBetta\admin\controllers;

use BaseController;
use TopBetta\Repositories\FreeCreditTransactionRepo;
use User;
use View;

class UserFreeCreditTransactionsController extends BaseController
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var FreeCreditTransactionRepo
	 */
	private $freeCreditTransactionRepo;

	public function __construct(FreeCreditTransactionRepo $freeCreditTransactionRepo, User $user)
	{

		$this->freeCreditTransactionRepo = $freeCreditTransactionRepo;
		$this->user = $user;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId)
	{
		$user = $this->user->find($userId);
		$transactions = $this->freeCreditTransactionRepo->userTransactions($user->id);

		return View::make('admin::transactions.user.index')
						->with(compact('transactions', 'user'))
						->with('title', 'Free Credit')
						->with('active', 'free-credit-transactions');
	}



}
