<?php namespace TopBetta\admin\controllers;


use View;
use Redirect;
use TopBetta\Services\Processes\RemoveFreeCreditsFromDormantUsersProcess;
use TopBetta\Services\UserAccount\UserFreeCreditService;

class FreeCreditManagementController extends \BaseController {

	const REMOVE_CREDITS_DEFAULT_DAYS = 60;

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
		return View::make("admin::freecredit.index")->with(array("defaultDays" => self::REMOVE_CREDITS_DEFAULT_DAYS));
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

	public function removeDormantCredits()
	{

		$days = \Input::get("days", self::REMOVE_CREDITS_DEFAULT_DAYS);

		//make sure $days is an integer
		if(! preg_match("/^[0-9]+$/", $days) )
		{
			return Redirect::route("admin.free-credit-management.index")->with("flash_message", "Invalid Days");
		}

		$this->removeFreeCreditsFromDormantUsersProcess->setDormantDays($days);
		try{
			$this->removeFreeCreditsFromDormantUsersProcess->run();
		} catch (\Exception $e) {
			return Redirect::route("admin.free-credit-management.index")->with("flash_message", "An unexpected error occured with message: ".$e->getMessage());
		}


		return Redirect::route("admin.free-credit-management.index")->with("flash_message", "Success!");
	}


}
