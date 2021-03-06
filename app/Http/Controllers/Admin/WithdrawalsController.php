<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use Carbon\Carbon;
use Request;
use View;
use Auth;
use Input;
use Redirect;

use TopBetta\Repositories\WithdrawalsRepo;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\WithdrawalService;


class WithdrawalsController extends Controller
{

	/**
	 * @var WithdrawalsRepo
	 */
	private $withdrawalRepo;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var WithdrawalService
     */
    private $withdrawalService;

    public function __construct(WithdrawalsRepo $withdrawalRepo, AccountTransactionService $accountTransactionService, WithdrawalService $withdrawalService)
	{
		$this->withdrawalRepo = $withdrawalRepo;
        $this->accountTransactionService = $accountTransactionService;
        $this->withdrawalService = $withdrawalService;
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');

        //show all or pending
        $pending = Input::get('pending', false);

		if ($search) {
			$withdrawals = $this->withdrawalRepo->search($search);
		} else {
            if($pending) {
                $withdrawals = $this->withdrawalRepo->allPendingWithdrawals();
            } else {
                $withdrawals = $this->withdrawalRepo->allWithdrawals();
            }
		}

		return View::make('admin.withdrawals.index', compact('withdrawals', 'search', 'pending'));
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
		$withdrawal = $this->withdrawalRepo->find($id);

        if( $withdrawal->approved_flag ) {
            return Redirect::route('admin.withdrawals.index')
                ->with(array('flash_message' => "Withdrawal already processed"));
        }

        return View::make('admin.withdrawals.edit', compact('withdrawal'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = Input::except('transaction_notes');

        $withdrawal = $this->withdrawalRepo->find($id);

		$email_flag = $data['email_flag'];
		unset($data['email_flag']);

        //can the user withdraw this amount?
        if($withdrawal->amount > $this->accountTransactionService->getAvailableWithdrawalBalance($withdrawal->user->id) && $data['approved_flag'] == 1) {
            return Redirect::route('admin.withdrawals.edit', array($withdrawal->id))->with(array("flash_message" => "Amount is greater than available withdrawal balance"));
        }

        //set fulfiller info
        $data['fulfilled_date'] = Carbon::now()->toDateTimeString();
        $data['fulfiller_id'] = Auth::user()->id;

		// account balance and time of processing
		$data['processed_amount'] = $withdrawal->user->accountBalance();

		$withdrawal->update($data);

        //create transaction
        if($data['approved_flag']) {
			if($email_flag) $this->withdrawalService->sendApprovalEmail($id);
            if(!$accountTransaction = $this->accountTransactionService->decreaseAccountBalance($withdrawal->user->id, $withdrawal->amount, 'withdrawal', \Auth::user()->id, \Input::get('transaction_notes'))){
				return Redirect::route('admin.withdrawals.edit', array($withdrawal->id))->with(array("flash_message" => "Account Tranasction Failed!"));
			}
			$data['notes'] = 'Transaction ID: '.$accountTransaction['id']. ' - '.$data['notes'];
        } else {
			if($email_flag) $this->withdrawalService->sendDenialEmail($id);
        }

		// update notes
		$withdrawal->update($data);

        return \Redirect::route('admin.withdrawals.index')
            ->with(array('flash_message'=>"Saved!"));
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
