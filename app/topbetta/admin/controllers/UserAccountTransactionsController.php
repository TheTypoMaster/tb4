<?php

namespace TopBetta\admin\controllers;

use BaseController;
use Carbon\Carbon;
use TopBetta\Repositories\AccountTransactionRepo;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
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
	 * @var AccountTransactionRepo
	 */
	private $accountTransactionRepo;
    /**
     * @var AccountTransactionTypeRepositoryInterface
     */
    private $accountTransactionTypeRepository;

    public function __construct(AccountTransactionRepositoryInterface $accountTransactionRepo,
                                AccountTransactionTypeRepositoryInterface $accountTransactionTypeRepository,
                                User $user)
	{

		$this->accountTransactionRepo = $accountTransactionRepo;
		$this->user = $user;
        $this->accountTransactionTypeRepository = $accountTransactionTypeRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId)
	{
		$user = $this->user->find($userId);
		$transactions = $this->accountTransactionRepo->getUserTransactionsPaginated($user->id);

		return View::make('admin::transactions.user.index')
						->with(compact('transactions', 'user'))
						->with('title', 'Account')
						->with('active', 'account-transactions');
	}

    public function create($userId)
    {
        $user = $this->user->find($userId);

        $transactionTypes = $this->accountTransactionTypeRepository->findAll();

        return View::make('admin::transactions.create', compact('user', 'transactionTypes'))
            ->with('title', 'Account')
            ->with('active', 'account-transactions');
    }

    public function store($userId)
    {
        $data = Input::all();

        $data['amount'] = 100*$data['amount'];
        $data['created_date'] = Carbon::now()->toDateTimeString();
        $data['recipient_id'] = $userId;
        $data['giver_id'] = \Auth::user()->id;
        $data['session_tracking_id'] = -1;

        $this->accountTransactionRepo->create($data);

        return Redirect::route('admin.users.account-transactions.index', array($userId));
    }

}
