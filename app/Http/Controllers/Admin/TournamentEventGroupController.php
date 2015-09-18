<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Tournaments\TournamentEventGroupEventService;
use TopBetta\Services\Tournaments\TournamentEventGroupService;
use Input;

class TournamentEventGroupController extends Controller
{

    public function __construct(TournamentEventGroupService $tournamentEventGroupService, TournamentEventGroupEventService $tournamentEventGroupEventService,
                                EventService $eventService) {

        $this->tournamentEventGroupService = $tournamentEventGroupService;
        $this->tournamentEventGroupEventService = $tournamentEventGroupEventService;
        $this->eventService = $eventService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $event_groups = $this->tournamentEventGroupService->getAllEventGroups();
        return view('admin.tournaments.event-groups.index')->with(['event_groups' => $event_groups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $event_group_list = $this->tournamentEventGroupService->getAllEventGroupsToArray();

        $events = $this->eventService->getAllEventsFromToday();

        return view('admin.tournaments.event-groups.create')->with(['event_group_list' => $events]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
//        $tournamentEventGroupModel = TournamentEventGroupModel::create(['name' => Input::get('event_group_name')]);
        $event_group_params = array('name' => Input::get('event_group_name'));
        $event_group = $this->tournamentEventGroupService->createEventGroup($event_group_params);
        $selected_events = Input::get('events');
        $items = array();
        $items['id'] = $event_group['id'];
        foreach($selected_events as $selected_event) {
            $items[] = $selected_event;
        }

        $new_event_group_events = $this->tournamentEventGroupEventService->createEventGroupEvent($items);
        return redirect()->action('Admin\TournamentEventGroupController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
