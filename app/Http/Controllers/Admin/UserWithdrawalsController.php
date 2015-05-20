<?php

namespace TopBetta\admin\controllers;

use BaseController;
use Redirect;
use TopBetta\Repositories\WithdrawalsRepo;
use User;
use View;

class UserWithdrawalsController extends BaseController
{

	/**
	 * @var WithdrawalsRepo
	 */
	private $withdrawalsRepo;
	protected $user;

	public function __construct(User $user, WithdrawalsRepo $withdrawalsRepo)
	{
		$this->user = $user;
		$this->withdrawalsRepo = $withdrawalsRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId)
	{
		$user = $this->user->find($userId);

		if (is_null($user)) {
			return Redirect::route('admin.users.index')
							->with('flash_message', 'User not found!');
		}

		$withdrawals = $this->withdrawalsRepo->getUserWithdrawals($user->id);

		return View::make('admin::withdrawals.user.index', compact('user', 'withdrawals'))
						->with('active', 'withdrawals');
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
