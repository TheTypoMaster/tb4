<?php namespace TopBetta\Http\Controllers\Admin;


use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\PlayersRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;
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

		$selection = $this->selectionsrepo->find($id)->load(array('team'));

        if (is_null($selection)) {
            // TODO: flash message user not found
            return Redirect::route('admin.selections.index');
        }

        $teams = array_merge(array(0 => ''), $this->teamRepository->findAll()->lists('name', 'id')->all());
        $players = array_merge(array(0 => ''), $this->playersRepository->findAll()->lists('name', 'id')->all());

        return View::make('admin.eventdata.selections.edit', compact('selection', 'search', 'players', 'teams'));
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

        //get the selection
        $selection = $this->selectionsrepo->find($id);

		$data = Input::only('name', 'selection_status_id', 'team', 'player');

        //ignore 0 values
        if(array_get($data, 'team', false)) {
            $data['team'] = array_filter($data['team'], function($value) {
                return $value != 0;
            });
        }

        if(array_get($data, 'player', false)) {
            $data['player'] = array_filter($data['player'], function($value) {
                return $value != 0;
            });
        }

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
