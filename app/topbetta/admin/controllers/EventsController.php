<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;
use View;
use BaseController;
use Redirect;
use Input;

class EventsController extends BaseController
{

	/**
	 * @var \TopBetta\Repositories\DbEventsRepository
	 */
	private $eventsrepo;
	private $eventstatusrepo;
    private $teamRepository;

    public function __construct(EventRepositoryInterface $eventsrepo,
								EventStatusRepositoryInterface $eventstatusrepo,
                                TeamRepositoryInterface $teamRepository)
	{
		$this->eventsrepo = $eventsrepo;
		$this->eventstatusrepo = $eventstatusrepo;
        $this->teamRepository = $teamRepository;
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
			$events = $this->eventsrepo->search($search);
		} else {
			$events = $this->eventsrepo->allEvents();
		}

		return View::make('admin::eventdata.events.index', compact('events', 'search'));
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
        //Get the search string if it exists so after updating we redirect back to filtered view
		$search = Input::get("q", '');
		$event = $this->eventsrepo->find($id);

        if (is_null($event)) {
            // TODO: flash message events not found
            return Redirect::route('admin.events.index');
        }

		$event_status = $this->eventstatusrepo->getEventStatusList();

        $teams = $this->teamRepository->findAll();

        return View::make('admin::eventdata.events.edit', compact('event', 'event_status', 'teams', 'search'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        //Get the query string for use when redirecting
		$search = Input::get("q", '');
		$data = Input::except(array("q", 'teams', 'team_position'));

		// deal with mysql setting '' on an integer field!!!!
		if($data['number'] == '') $data['number'] = NULL;

        $this->eventsrepo->updateWithId($id, $data);

        //get team info
        $teams = Input::get('teams', array());
        $teamPositions = Input::get('team_position', array());

        $teams = array_combine($teams, array_map( function($value) { return array("team_position" => $value); }, $teamPositions));

        $this->eventsrepo->addTeams($id, array_except($teams, 0));

        return Redirect::route('admin.events.index', array($id, "q" => $search))
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
