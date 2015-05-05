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
        //Get search string for filtering after redirection
		$search = Input::get("q", '');
        $market = Input::get('market', null);
        $event = Input::get('event', null);

		$selectionprice = $this->selectionpricesrepo->findWithSelection($id);

        if (is_null($selectionprice)) {
            // TODO: flash message user not found
            return Redirect::route('admin.selectionprices.index');
        }

        if($selectionprice->override_type == 'percentage') { $selectionprice->override_odds *= 100; }

        return View::make('admin::eventdata.selectionprices.edit', compact('selectionprice', 'search', 'market', 'event'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        //Get search string for filtering after redirection
		$search = Input::get("q", '');
        $market = Input::get('market', null);

		$data = Input::only('override_odds', 'override_type');

        if($data['override_type'] == 'percentage') {
            $data['override_odds'] /= 100;
        } else if (is_null($data['override_type'])) {
            return Redirect::route('admin.selectionprices.edit', array($id, 'q' => $search, 'market' => $market))
                ->with(array('flash_message' => "Select Type"));
        }

        $this->selectionpricesrepo->updateWithId($id, $data);

        return Redirect::route('admin.selections.index', array($id, 'q' => $search, 'market' => $market))
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
