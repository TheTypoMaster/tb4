<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\DbSportsRepository;
use View;
use BaseController;
use Redirect;
use Input;

class SportsController extends BaseController
{

	/**
	 * @var \TopBetta\Repositories\DbSportsRepository
	 */
	private $sportsrepo;

	public function __construct(DbSportsRepository $sportsrepo)
	{
		$this->sportsrepo = $sportsrepo;
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
			$sports = $this->sportsrepo->search($search);
		} else {
			$sports = $this->sportsrepo->allSports();
		}

		return View::make('admin::eventdata.sports.index', compact('sports', 'search'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('admin::eventdata.sports.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $data = Input::only('name', 'description');
        $newModel = $this->sportsrepo->updateOrCreate($data);

        return Redirect::route('admin.sports.index', array($newModel['id']))
            ->with('flash_message', 'Saved!');
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
		//get search query so we remain filtered when redirecting after editing
        $search = Input::get("q", '');
		$sport = $this->sportsrepo->find($id);

        if (is_null($sport)) {
            // TODO: flash message user not found
            return Redirect::route('admin.sports.index', array("q" => $search));
        }

        return View::make('admin::eventdata.sports.edit', compact('sport', 'search'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        //Get the search string for filtering when redirecting
		$search = Input::get("q", '');

		$data = Input::only('name', 'description');
        $this->sportsrepo->updateWithId($id, $data);

        return Redirect::route('admin.sports.index', array($id, "q"=>$search))
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

    public function getSports()
    {
        return $this->sportsrepo->sportsFeed();
    }

}
