<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\ReportRepo;
use Request;
use View;

class ReportsController extends Controller
{

	/**
	 * @var ReportRepo
	 */
	private $reportRepo;

	public function __construct(ReportRepo $reportRepo)
	{

		$this->reportRepo = $reportRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		return View::make('admin.reports.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($report)
	{
		$month = Request::get('month', date('m'));
		$year = Request::get('year', date('Y'));
		$download = Request::get('download', false);
		$params = Request::except(array('page'));
		
		$data = null;

		switch ($report) {
			case 'tournaments':
				$data = $this->reportRepo->tournamentForMonthYear($month, $year, $download);
				break;

			case 'bets':
				$data = $this->reportRepo->betsForMonthYear($month, $year, $download);
				break;

			default:
				break;
		}


		if ($download) {
			return $data;
		}

		return View::make('admin.reports.show')
						->with(compact('data', 'month', 'year', 'report', 'params'));
	}

}
