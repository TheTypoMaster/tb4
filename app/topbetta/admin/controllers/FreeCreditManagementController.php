<?php namespace TopBetta\admin\controllers;



use TopBetta\Services\UserAccount\UserFreeCreditService;

class FreeCreditManagementController extends \BaseController {

	/**
	 * @var UserFreeCreditService
	 */
	private $userFreeCreditService;

	public function __construct(UserFreeCreditService $userFreeCreditService)
	{
		$this->userFreeCreditService = $userFreeCreditService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->userFreeCreditService->removeCreditsFromInactiveUsers(60);
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
