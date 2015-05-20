<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface;
use TopBetta\Repositories\FreeCreditTransactionRepo;
use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;
use TopBetta\Services\UserAccount\UserFreeCreditService;
use Redirect;
use TopBetta\Models\UserModel;
use View;
use Input;
use Auth;

class UserFreeCreditTransactionsController extends Controller
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var FreeCreditTransactionRepo
	 */
	private $freeCreditTransactionRepo;
    /**
     * @var FreeCreditTransactionTypeRepositoryInterface
     */
    private $freeCreditTransactionTypeRepository;
    /**
     * @var UserFreeCreditService
     */
    private $freeCreditService;
    /**
     * @var UserDashboardNotificationService
     */
    private $dashboardNotificationService;

    public function __construct(FreeCreditTransactionRepo $freeCreditTransactionRepo,
                                UserModel $user,
                                FreeCreditTransactionTypeRepositoryInterface $freeCreditTransactionTypeRepository,
                                UserFreeCreditService $freeCreditService,
                                UserDashboardNotificationService $dashboardNotificationService)
	{

		$this->freeCreditTransactionRepo = $freeCreditTransactionRepo;
		$this->user = $user;
        $this->freeCreditTransactionTypeRepository = $freeCreditTransactionTypeRepository;
        $this->freeCreditService = $freeCreditService;
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
		$transactions = $this->freeCreditTransactionRepo->userTransactions($user->id);
        $createRoute = 'admin.users.free-credit-transactions.create';

		return View::make('admin.transactions.user.index')
						->with(compact('transactions', 'user', 'createRoute'))
						->with('title', 'Free Credit')
						->with('active', 'free-credit-transactions');
	}

    public function create($userId)
    {
        $user = $this->user->find($userId);

        $transactionTypes = $this->freeCreditTransactionTypeRepository->findAll();

        return View::make('admin.transactions.create', compact('user', 'transactionTypes'))
            ->with('title', 'Free Credit')
            ->with('storeRoute', 'admin.users.free-credit-transactions.store')
            ->with('active', 'free-credit-transactions');

    }

    public function store($userId)
    {
        $data = Input::all();

        $transactionType = $this->freeCreditTransactionTypeRepository->getIdByName($data['transaction_type']);

        $transaction = $this->freeCreditService->increaseFreeCreditBalance($userId, \Auth::user()->id, $data['amount']*100, $transactionType, $data['notes']);

        if( $transaction ) {
            $this->dashboardNotificationService->notify(array("id" => $userId, "free-credit-transactions" => array($transaction['id'])));
        }

        return Redirect::route('admin.users.free-credit-transactions.index', array($userId));
    }



}
