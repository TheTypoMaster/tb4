<?php

namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Models\BetModel;
use View;

class BetsController extends Controller
{

	/**
	 * Bet Repository
	 *
	 * @var Bet
	 */
	protected $bet;

	public function __construct(BetModel $bet)
	{
		$this->bet = $bet;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$bets = $this->bet
				->with(array(
					'betselection.selection',
					'status',
					'result',
					'user',
					'type'
				))
				->orderBy('created_date', 'desc')
				->paginate();

		return View::make('admin.bets.index', compact('bets'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('admin.bets.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Bet::$rules);

		if ($validation->passes()) {
			$this->bet->create($input);

			return Redirect::route('admin.bets.index');
		}

		return Redirect::route('bets.create')
						->withInput()
						->withErrors($validation)
						->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$bet = $this->bet->findOrFail($id);

		return View::make('admin.bets.show', compact('bet'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$bet = $this->bet->find($id);

		if (is_null($bet)) {
			return Redirect::route('admin.bets.index');
		}

		return View::make('bets.edit', compact('bet'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Bet::$rules);

		if ($validation->passes()) {
			$bet = $this->bet->find($id);
			$bet->update($input);

			return Redirect::route('admin.bets.show', $id);
		}

		return Redirect::route('admin.bets.edit', $id)
						->withInput()
						->withErrors($validation)
						->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->bet->find($id)->delete();

		return Redirect::route('admin.bets.index');
	}

}
