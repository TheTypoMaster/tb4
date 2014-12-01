<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\DbMarketsRepository;
use View;
use BaseController;
use Redirect;
use Input;

class MarketsController extends BaseController
{

	/**
	 * @var \TopBetta\Repositories\DbMarketsRepository
	 */
	private $marketsrepo;

	public function __construct(DbMarketsRepository $marketsrepo)
	{
		$this->marketsrepo = $marketsrepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');
		if ($search) {
			$markets = $this->marketsrepo->search($search);
		} else {
			$markets = $this->marketsrepo->allMarkets();
		}

        return View::make('admin::eventdata.markets.index', compact('markets', 'search'));
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

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $market = $this->marketsrepo->findWithMarketTypePlusEvent($id);

        if (is_null($market)) {
            // TODO: flash message user not found
            return Redirect::route('admin.markets.index');
        }

        return View::make('admin::eventdata.markets.edit', compact('market'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $data = Input::only('market_status', 'display_flag');
        $this->marketsrepo->updateWithId($id, $data);

        return Redirect::route('admin.markets.index', array($id))
            ->with('flash_message', 'Saved!');
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
