<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use TopBetta;
use TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface;

class FrontUsersBalancesController extends Controller {

    /**
     * @var UserTopBettaRepositoryInterface
     */
    private $userTopBettaRepository;

    public function __construct(UserTopBettaRepositoryInterface $userTopBettaRepository)
	{
		$this->beforeFilter('auth');
        $this->userTopBettaRepository = $userTopBettaRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{			
		$userId = \Auth::user() -> id;
		
		$accountBalance = \TopBetta\Models\AccountBalance::getAccountBalance($userId);
		$freeCreditBalance = \TopBetta\Models\FreeCreditBalance::getFreeCreditBalance($userId);

        $topBettaUser = $this->userTopBettaRepository->getUserDetailsFromUserId($userId);
		
		return array("success" => true, "result" => array("account_balance" => $accountBalance, "freecredit_balance" => $freeCreditBalance, "balance_to_turnover" => $topBettaUser['balance_to_turnover']));
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