<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\UserDepositLimitModel;
use View;
use Input;
use Redirect;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class UserDepositLimitsController extends Controller {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$user = $this->userRepository->find($id);

        $depositLimit = $user->depositLimit;

        return View::make('admin.depositlimits.user.index', compact('user', 'depositLimit'))
            ->with('active', 'deposit-limit');
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
		$user = $this->userRepository->find($id);

        $depositLimit = $user->depositLimit;

        return View::make("admin.depositlimits.user.edit", compact('user', 'depositLimit'))
            ->with('active', 'deposit-limit');
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = Input::only('amount', 'notes');

        $user = $this->userRepository->find($id);

        if( ! $user->depositLimit ) {
            $depositLimit = new UserDepositLimitModel($data);
            $user->depositLimit()->save($depositLimit);
        } else {
            $user->depositLimit->update($data);
        }

        return Redirect::route('admin.users.deposit-limit.index', array($user->id));

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
