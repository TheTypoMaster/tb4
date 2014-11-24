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
        $sport = $this->sportsrepo->find($id);

        if (is_null($sport)) {
            // TODO: flash message user not found
            return Redirect::route('admin.sports.index');
        }

        return View::make('admin::eventdata.sports.edit', compact('sport'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $data = Input::only('name', 'description');
        $this->sportsrepo->updateWithId($id, $data);

        return Redirect::route('admin.sports.index', array($id))
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
