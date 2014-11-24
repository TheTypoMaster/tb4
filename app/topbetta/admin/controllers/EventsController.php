<?php namespace TopBetta\admin\controllers;

use Request;
use TopBetta\Repositories\DbEventsRepository;
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

	public function __construct(DbEventsRepository $eventsrepo)
	{
		$this->eventsrepo = $eventsrepo;
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
        $event = $this->eventsrepo->find($id);

        if (is_null($event)) {
            // TODO: flash message user not found
            return Redirect::route('admin.events.index');
        }

        return View::make('admin::eventdata.events.edit', compact('event'));
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
        $this->eventsrepo->updateWithId($id, $data);

        return Redirect::route('admin.events.index', array($id))
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
