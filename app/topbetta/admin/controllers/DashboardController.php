<?php

namespace TopBetta\admin\controllers;

use Carbon\Carbon;
use User;
use View;

class DashboardController extends \BaseController
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// TODO: move to repository
		$today = Carbon::now()->toDateString();
		$totals = array(
			'account_balances' => array(
				'title' => 'Total Account Balances',
				'amount' => '$' . number_format(\TopBetta\AccountBalance::sum('amount') / 100, 2)
			),
			'free_credit_balances' => array(
				'title' => 'Total Free Credit Balances',
				'amount' => '$' . number_format(\TopBetta\FreeCreditBalance::sum('amount') / 100, 2)
			),			
			'todays_signups' => array(
				'title' => 'Registered Today',
				'amount' => User::where('registerDate', 'LIKE', $today . '%')->count()
			),			
			'todays_bets_total' => array(
				'title' => 'Total Bets Today / (FC)',
				'amount' => \TopBetta\Bet::where('created_date', 'LIKE', $today . '%')->count() . 
				' / (' . \TopBetta\Bet::where('created_date', 'LIKE', $today . '%')->where('bet_freebet_flag', 1)->count() . ')'
			),			
			'todays_bets_amount' => array(
				'title' => 'Total Bet Amount Today (inc. FC)',
				'amount' => '$' . number_format(\TopBetta\Bet::where('created_date', 'LIKE', $today . '%')->sum('bet_amount` + `bet_freebet_amount') / 100, 2)
			),			
		);

		return View::make('admin::dashboard.index')
						->with(compact('balances', 'totals'));
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
