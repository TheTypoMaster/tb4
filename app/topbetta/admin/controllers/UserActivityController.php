<?php

namespace TopBetta\admin\controllers;

use Response;
use Input;
use File;
use View;
use Carbon\Carbon;
use TopBetta\Services\UserAccount\UserReportService;

class UserActivityController extends \BaseController {

    /**
     * @var UserReportService
     */
    private $userReportService;

    public function __construct(UserReportService $userReportService)
    {
        $this->userReportService = $userReportService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('admin::users.user-activity');
	}

    public function downloadUserActivity()
    {
        $users = Input::get('users', '');

        $users = array_map(function($v){
            return array_combine(array('first_name', 'last_name', 'dob'), explode(' ', $v));
        }, explode(PHP_EOL, $users));

        //get the activity data
        $data = $this->userReportService->userTransactionHistoryByNameDOB($users);

        $filename = '/tmp/user-activity-' . Carbon::now()->timestamp . '.csv';
        $csv = fopen($filename, 'w');

        //headers
        fputcsv($csv, array(
            "First name",
            "Last Name",
            "Amount",
            "Notes",
            "Transaction Type",
            "Selection",
            "Market",
            "Event",
            "Competition",
            "Sport",
            "Buy in",
            "Entry",
        ));

        //create csv
        foreach($data as $record) {
            fputcsv($csv, $record);
        }
        fclose($csv);

        //download file
        $response = Response::make(file_get_contents($filename), 200, array("Content-type" => "text/csv; charset=UTF-8", "Content-disposition" => "attachment; filename=user-activity.csv"));

        File::delete($filename);

        return $response;
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
