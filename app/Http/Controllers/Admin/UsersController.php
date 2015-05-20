<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use Redirect;
use TopBetta\Models\BetModel;
use TopBetta\Facades\BetLimitRepo;
use TopBetta\Repositories\UserRepo;
use TopBetta\Models\UserModel;
use Request;
use View;

class UsersController extends Controller
{

	/**
	 * @var UserRepo
	 */
	private $userRepo;
	protected $user;
	private $bet;

	public function __construct(UserModel $user, UserRepo $userRepo, BetModel $bet)
	{
		$this->user = $user;
		$this->bet = $bet;
		$this->userRepo = $userRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q','');
		if ($search) {
			$users = $this->userRepo->search($search);
		} else {
			$users = $this->userRepo->allUsers();
		}

		return View::make('admin.users.index')
						->with(compact('users','search'));
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
		$user = $this->user->find($id);

		if (is_null($user)) {
			// TODO: flash message user not found
			return Redirect::route('admin.users.index');
		}

		return View::make('admin.users.edit', compact('user'))
						->with('active', 'profile');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($userId)
	{
		return Redirect::route('admin.users.edit', array($userId))
						->with('flash_message', 'NOT IMPLEMENTED YET!');
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
