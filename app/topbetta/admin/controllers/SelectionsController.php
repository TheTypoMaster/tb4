<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\Contracts\PlayersRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;
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
    /**
     * @var TeamRepositoryInterface
     */
    private $teamRepository;
    /**
     * @var PlayersRepositoryInterface
     */
    private $playersRepository;

    public function __construct(DbSelectionRepository $selectionsrepo,
                                TeamRepositoryInterface $teamRepository,
                                PlayersRepositoryInterface $playersRepository)
	{
		$this->selectionsrepo = $selectionsrepo;
        $this->teamRepository = $teamRepository;
        $this->playersRepository = $playersRepository;
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
        //Get search string for filtering after redirection
		$search = Input::get("q", '');

		$selection = $this->selectionsrepo->find($id);

        if (is_null($selection)) {
            // TODO: flash message user not found
            return Redirect::route('admin.selections.index');
        }

        $teams = $this->formatCollectionForSelect($this->teamRepository->findAll(), true);
        $players = $this->formatCollectionForSelect($this->teamRepository->findAll(), true);

        return View::make('admin::eventdata.selections.edit', compact('selection', 'search', 'players', 'teams'));
	}

    private function formatCollectionForSelect($collection, $emptySelection = false)
    {
        $select = array();

        if( $emptySelection ) {
            $select[0] = "";
        }

        foreach($collection as $model) {
            $select[$model->id] = $model->name;
        }

        return $select;
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

		$data = Input::only('name', 'selection_status_id');
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
