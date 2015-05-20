<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;

use Request;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use View;
use Redirect;
use Input;

class EventsController extends Controller
{

	/**
	 * @var \TopBetta\Repositories\DbEventsRepository
	 */
	private $eventsrepo;
	private $eventstatusrepo;

	public function __construct(EventRepositoryInterface $eventsrepo,
								EventStatusRepositoryInterface $eventstatusrepo)
	{
		$this->eventsrepo = $eventsrepo;
		$this->eventstatusrepo = $eventstatusrepo;
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

		return View::make('admin.eventdata.events.index', compact('events', 'search'));
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

        return View::make('admin.eventdata.events.edit', compact('event', 'event_status', 'search'));
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
		$data = Input::except(array("q"));

		// deal with mysql setting '' on an integer field!!!!
		if($data['number'] == '') $data['number'] = NULL;

        $this->eventsrepo->updateWithId($id, $data);

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
