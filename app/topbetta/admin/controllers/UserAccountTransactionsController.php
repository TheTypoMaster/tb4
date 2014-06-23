<?php

namespace TopBetta\admin\controllers;

use BaseController;
use TopBetta\Repositories\AccountTransactionRepo;
use User;
use View;

class UserAccountTransactionsController extends \BaseController
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var AccountTransactionRepo
	 */
	private $accountTransactionRepo;

	public function __construct(AccountTransactionRepo $accountTransactionRepo, User $user)
	{

		$this->accountTransactionRepo = $accountTransactionRepo;
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
		$transactions = $this->accountTransactionRepo->userTransactions($user->id);

		return View::make('admin::transactions.user.index')
						->with(compact('transactions', 'user'))
						->with('title', 'Account')
						->with('active', 'account-transactions');
	}

}
