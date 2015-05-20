<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\DbSelectionRepository;
use Request;
use View;
use Redirect;
use Input;
use Paginator;

class SelectionsController extends Controller
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

      //  $page = Input::get('page', 1);
        $market = Input::get('market', null);
        $event = Input::get('event', null);

//        $data = $this->selectionsrepo->allSelections($page, 50);

        $search = Request::get('q', '');
        if ($search) {
            $selections = $this->selectionsrepo->search($search, $market);
        } else if ($market) {
            $selections = $this->selectionsrepo->getAllSelectionsForMarket($market);
        }else {
			$selections = $this->selectionsrepo->allSelections(50);
		}

		return View::make('admin.eventdata.selections.index', compact('selections', 'search', 'market', 'event'));
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

		$selection = $this->selectionsrepo->find($id);

        if (is_null($selection)) {
            // TODO: flash message user not found
            return Redirect::route('admin.selections.index');
        }

        return View::make('admin.eventdata.selections.edit', compact('selection', 'search'));
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

		$data = Input::only('name', 'selection_status_id', 'display_flag');
        $this->selectionsrepo->updateWithId($id, $data);

        return Redirect::route('admin.selections.index', array($id, 'q'=>$search))
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
