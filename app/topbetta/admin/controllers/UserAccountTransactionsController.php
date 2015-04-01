<?php

namespace TopBetta\admin\controllers;

use BaseController;
use Carbon\Carbon;
use TopBetta\Repositories\AccountTransactionRepo;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;
use User;
use View;
use Input;
use Redirect;

class UserAccountTransactionsController extends \BaseController
{

	/**
	 * @var User
	 */
	private $user;
    /**
     * @var AccountTransactionTypeRepositoryInterface
     */
    private $accountTransactionTypeRepository;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var UserDashboardNotificationService
     */
    private $dashboardNotificationService;

    public function __construct(AccountTransactionService $accountTransactionService,
                                AccountTransactionTypeRepositoryInterface $accountTransactionTypeRepository,
                                User $user,
                                UserDashboardNotificationService $dashboardNotificationService)
	{

		$this->user = $user;
        $this->accountTransactionTypeRepository = $accountTransactionTypeRepository;
        $this->accountTransactionService = $accountTransactionService;
        $this->dashboardNotificationService = $dashboardNotificationService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId)
	{
		$user = $this->user->find($userId);
		$transactions = $this->accountTransactionService->getAccountTransactionsForUserPaginated($user->id);
        $createRoute = 'admin.users.account-transactions.create';

		return View::make('admin::transactions.user.index')
						->with(compact('transactions', 'user', 'createRoute'))
						->with('title', 'Account')
						->with('active', 'account-transactions');
	}

    public function create($userId)
    {
        $user = $this->user->find($userId);

        $transactionTypes = $this->accountTransactionTypeRepository->findAll();

        return View::make('admin::transactions.create', compact('user', 'transactionTypes'))
            ->with('title', 'Account')
            ->with('storeRoute', 'admin.users.account-transactions.store')
            ->with('active', 'account-transactions');
    }

    public function store($userId)
    {
        $data = Input::all();

        $transaction = $this->accountTransactionService->increaseAccountBalance($userId, $data['amount']*100, $data['transaction_type'], \Auth::user()->id, $data['notes']);

        $this->dashboardNotificationService->notify(array('id' => $userId, 'transactions' => array($transaction['id'])));

        return Redirect::route('admin.users.account-transactions.index', array($userId));
    }

}
