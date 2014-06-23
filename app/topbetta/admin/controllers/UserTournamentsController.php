<?php

namespace TopBetta\admin\controllers;

use TopBetta\Repositories\UserRepo;
use View;
use User;

class UserTournamentsController extends \BaseController
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var UserRepo
	 */
	private $userRepo;

	public function __construct(User $user, UserRepo $userRepo)
	{

		$this->userRepo = $userRepo;
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
		$tournaments = $this->userRepo->tournaments($user->id);

		return View::make('admin::tournaments.user.index')
						->with(compact('user', 'tournaments'))
						->with('active', 'tournaments');
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
