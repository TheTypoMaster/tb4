<?php namespace TopBetta\admin\controllers;



use TopBetta\Services\Processes\RemoveFreeCreditsFromDormantUsersProcess;
use TopBetta\Services\UserAccount\UserFreeCreditService;

class FreeCreditManagementController extends \BaseController {

	/**
	 * @var RemoveFreeCreditsFromDormantUsersProcess
	 */
	private $removeFreeCreditsFromDormantUsersProcess;

	public function __construct(RemoveFreeCreditsFromDormantUsersProcess $removeFreeCreditsFromDormantUsersProcess)
	{

		$this->removeFreeCreditsFromDormantUsersProcess = $removeFreeCreditsFromDormantUsersProcess;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->removeFreeCreditsFromDormantUsersProcess->setDormantDays(60);
		return $this->removeFreeCreditsFromDormantUsersProcess->run();
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
