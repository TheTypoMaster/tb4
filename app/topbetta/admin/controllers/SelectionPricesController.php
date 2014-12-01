<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\DbSelectionPricesRepository;
use View;
use BaseController;
use Redirect;
use Input;
use Paginator;

class SelectionPricesController extends BaseController
{
	/**
	 * @var \TopBetta\Repositories\DbSelectionPriceRepository
	 */
	private $selectionpricesrepo;

	public function __construct(DbSelectionPricesRepository $selectionpricesrepo)
	{
		$this->selectionpricesrepo = $selectionpricesrepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

        $page = Input::get('page', 1);

        $data = $this->selectionpricesrepo->allSelectionPrices($page, 50);

		$search = Request::get('q', '');
		if ($search) {
			$selectionprices = $this->selectionpricesrepo->search($search);
		} else {
            $selectionprices = Paginator::make($data->items, $data->totalItems, 50);
		}

		return View::make('admin::eventdata.selectionprices.index', compact('selectionprices', 'search'));
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
        $selectionprice = $this->selectionpricesrepo->findWithSelection($id);

        if (is_null($selectionprice)) {
            // TODO: flash message user not found
            return Redirect::route('admin.selectionprices.index');
        }

        return View::make('admin::eventdata.selectionprices.edit', compact('selectionprice'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $data = Input::only('win_odds', 'place_odds');
        $this->selectionpricesrepo->updateWithId($id, $data);

        return Redirect::route('admin.selectionprices.index', array($id))
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
