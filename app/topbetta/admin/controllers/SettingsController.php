<?php

namespace TopBetta\admin\controllers;

use BaseController;
use BetLimitType;
use Input;
use Redirect;
use View;

class SettingsController extends BaseController
{

	/**
	 * @var BetLimitType
	 */
	private $betLimitType;

	public function __construct(BetLimitType $betLimitType)
	{

		$this->betLimitType = $betLimitType;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$betLimit = $this->betLimitType->find(1);
		$flexiLimit = $this->betLimitType->find(2);
		
		return View::make('admin::settings.index')
						->with(compact('betLimit', 'flexiLimit'));
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
		switch ($id) {
			case 'bet_limit':
				$betLimit = $this->betLimitType->findOrFail(1);
				$betLimit->default_amount = str_replace(',', '', Input::get('default_amount', 1000)) * 100;
				$betLimit->save();
				break;
			
			case 'bet_flexi':
				$betLimit = $this->betLimitType->findOrFail(2);
				$betLimit->default_amount = str_replace(',', '', Input::get('default_amount', 1000)) * 100;
				$betLimit->save();
				break;			

			default:
				break;
		}
		
		return Redirect::route('admin.settings.index')
				->with('flash_message', 'Settings updated.');
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
