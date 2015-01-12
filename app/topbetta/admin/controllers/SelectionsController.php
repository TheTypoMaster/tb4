<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\DbSelectionRepository;
use View;
use BaseController;
use Redirect;
use Input;
use Paginator;

class SelectionsController extends BaseController
{

	/**
	 * @var \TopBetta\Repositories\DbSelectionsRepository
	 */
	private $selectionsrepo;

	public function __construct(DbSelectionRepository $selectionsrepo)
	{
		$this->selectionsrepo = $selectionsrepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

        $page = Input::get('page', 1);

        $data = $this->selectionsrepo->allSelections($page, 50);

		$search = Request::get('q', '');
		if ($search) {
			$selections = $this->selectionsrepo->search($search);
		} else {
            $selections = Paginator::make($data->items, $data->totalItems, 50);
		}

		return View::make('admin::eventdata.selections.index', compact('selections', 'search'));
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
        $selection = $this->selectionsrepo->find($id);

        if (is_null($selection)) {
            // TODO: flash message user not found
            return Redirect::route('admin.selections.index');
        }

        return View::make('admin::eventdata.selections.edit', compact('selection'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $data = Input::only('name', 'selection_status_id');
        $this->selectionsrepo->updateWithId($id, $data);

        return Redirect::route('admin.selections.index', array($id))
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
