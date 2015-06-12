<?php namespace TopBetta\admin\controllers;

use Request;
use View;
use BaseController;
use Redirect;
use Input;

use TopBetta\Repositories\DbCompetitionRepository;
use TopBetta\Repositories\DbSportsRepository;
use TopBetta\Services\DataManagement\CompetitionService;

class CompetitionsController extends BaseController
{

	/**
	 * @var \TopBetta\Repositories\DbCompetitionsRepository
	 */
	protected $competitionsrepo;
    protected $competitionservice;

	public function __construct(DbCompetitionRepository $competitionsrepo,
                                CompetitionService $competitionservice,
                                DbSportsRepository $sportsrepo)
	{
		$this->competitionsrepo = $competitionsrepo;
		$this->competitionservice = $competitionservice;
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
        $sports = $this->sportsrepo->selectList();
        return View::make('admin::eventdata.competitions.create', compact('sports'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $this->competitionservice->createCompetition(Input::All());
        $competitions = $this->competitionsrepo->allCompetitions();

        $search = Request::get('q', '');
        return View::make('admin::eventdata.competitions.index', compact('competitions', 'search'));

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
        $search = Input::get('q', '');
		$competition = $this->competitionsrepo->find($id);

        if (is_null($competition)) {
            // TODO: flash message user not found
            return Redirect::route('admin.competitions.index');
        }

        return View::make('admin::eventdata.competitions.edit', compact('competition', 'search'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $search = Input::get("q", '');
        //$data = Input::only('name', 'description');
        $data = Input::except('q');
		if($data['state'] == '') $data['state'] = NULL;
		if($data['type_code'] == '') $data['type_code'] = NULL;

        $this->competitionsrepo->updateWithId($id, $data);

        return Redirect::route('admin.competitions.index', array($id, "q" => $search))
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

    public function getBySport($id)
    {
        return $this->competitionsrepo->findBySport($id)->toArray();
    }

}
