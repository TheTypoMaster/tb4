<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\DbCompetitionRepository;
use View;
use BaseController;
use Redirect;
use Input;

class CompetitionsController extends BaseController
{

	/**
	 * @var \TopBetta\Repositories\DbCompetitionsRepository
	 */
	private $competitionsrepo;

	public function __construct(DbCompetitionRepository $competitionsrepo)
	{
		$this->competitionsrepo = $competitionsrepo;
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
			$competitions = $this->competitionsrepo->search($search);
		} else {
			$competitions = $this->competitionsrepo->allCompetitions();
		}

		return View::make('admin::eventdata.competitions.index', compact('competitions', 'search'));
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
        $competition = $this->competitionsrepo->find($id);

        if (is_null($competition)) {
            // TODO: flash message user not found
            return Redirect::route('admin.competitions.index');
        }

        return View::make('admin::eventdata.competitions.edit', compact('competition'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        //$data = Input::only('name', 'description');
        $data = Input::all();
        $this->competitionsrepo->updateWithId($id, $data);

        return Redirect::route('admin.competitions.index', array($id))
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
