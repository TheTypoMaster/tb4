<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Sports\SportsService;
use TopBetta\Services\Tournaments\TournamentEventGroupEventService;
use TopBetta\Services\Tournaments\TournamentEventGroupService;
use Input;

class TournamentEventGroupController extends Controller
{

    public function __construct(TournamentEventGroupService $tournamentEventGroupService, TournamentEventGroupEventService $tournamentEventGroupEventService,
                                EventService $eventService,
                                SportsService $sportService)
    {

        $this->tournamentEventGroupService = $tournamentEventGroupService;
        $this->tournamentEventGroupEventService = $tournamentEventGroupEventService;
        $this->eventService = $eventService;
        $this->sportService = $sportService;
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

        $events = $this->eventService->getAllEventsFromToday();

        $sports = $this->sportService->getAllSports();

        return view('admin.tournaments.event-groups.create')->with(['event_group_list' => $events,
            'sport_list' => $sports]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
//        $tournamentEventGroupModel = TournamentEventGroupModel::create(['name' => Input::get('event_group_name')]);

//         dd(Input::get('event_group_id'));

        //if event_group_id is empty, create new event_group, otherwise use the already created event group for
        //continuing add new events
        if (Input::get('event_group_id') == '') {
            $event_group_params = array('name' => Input::get('event_group_name'));
            $event_group = $this->tournamentEventGroupService->createEventGroup($event_group_params);
            $event_group_id = $event_group['id'];
            $start_time = '';
            $end_time = '';

        } else {
            $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID(Input::get('event_group_id'));
            $event_group_id = Input::get('event_group_id');
            $event_group_params = array('name' => Input::get('event_group_name'));
            $start_time = $new_created_event_group->start_date;
            $end_time = $new_created_event_group->end_date;
        }


        $selected_events = Input::get('events');
        $items = array();
        $items['id'] = $event_group_id;

        //get the earliest start day from all events as event group start date

        foreach ($selected_events as $key => $selected_event) {

            $items[] = $selected_event;
            $event_start_time = $this->eventService->getEventByID($selected_event)->start_date;

            if ($key == 0 && $start_time == '') {
                $start_time = $event_start_time;
                $end_time = $event_start_time;

            } else {
                //set event group start date as the earliest event start date
                if ($event_start_time < $start_time) {

                    $start_time = $event_start_time;
                }

                //set event group end date as the latest event start date
                if ($event_start_time > $end_time) {
                    $end_time = $event_start_time;
                }
            }
        }


        //get the earliest created event group by id to set the event group start date, and the latest event start date as
        //event group end date
        $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID($event_group_id);
        $new_created_event_group->start_date = $start_time;
        $new_created_event_group->end_date = $end_time;
        $new_created_event_group->update();

        $new_event_group_events = $this->tournamentEventGroupEventService->createEventGroupEvent($items);
        return redirect()->action('Admin\TournamentEventGroupController@keepAdding', ['event_group_name' => $event_group_params['name'],
            'event_group_id' => $event_group_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $event_group = $this->tournamentEventGroupService->getEventGroupByID($id);

        return view('admin.tournaments.event-groups.edit')->with(['event_group' => $event_group]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * get event groups by sport id
     * @param $sportId
     * @return mixed
     */
    public function getEvnetGruops($sportId)
    {

        $event_groups = $this->tournamentEventGroupService->getEventGroups($sportId);
        return $event_groups;
    }

    /**
     * get events by event group id
     * @param $event_group_id
     * @return mixed
     */
    public function getEventsByEventGroup($event_group_id)
    {
        $event = $this->tournamentEventGroupService->getEventsByEventGroup($event_group_id);
//        dd($event);
        return $event;
    }

    /**
     * continue add new events to current event group
     * @param $group_name
     * @param $group_id
     * @return $this
     */
    public function keepAdding($group_name, $group_id)
    {

        $events = $this->eventService->getAllEventsFromToday();

        $sports = $this->sportService->getAllSports();

        return view('admin.tournaments.event-groups.create')->with(['event_group_list' => $events,
            'sport_list' => $sports,
            'event_group_name' => $group_name,
            'event_group_id' => $group_id]);
    }

}
