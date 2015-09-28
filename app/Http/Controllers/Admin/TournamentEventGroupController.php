<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Sports\SportsService;
use TopBetta\Services\Tournaments\TournamentEventGroupEventService;
use TopBetta\Services\Tournaments\TournamentEventGroupService;
use Input;

class TournamentEventGroupController extends Controller
{

    public function __construct(TournamentEventGroupService $tournamentEventGroupService, TournamentEventGroupEventService $tournamentEventGroupEventService,
                                EventService $eventService,
                                SportsService $sportService,
                                MeetingService $meetingService)
    {

        $this->tournamentEventGroupService = $tournamentEventGroupService;
        $this->tournamentEventGroupEventService = $tournamentEventGroupEventService;
        $this->eventService = $eventService;
        $this->sportService = $sportService;
        $this->meetingService = $meetingService;
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

//        $events = $this->eventService->getAllEventsFromToday();

        $sports = $this->sportService->getAllSports();

        return view('admin.tournaments.event-groups.create')->with(['sport_list' => $sports]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //if event_group_id is empty, create new event_group, otherwise use the already created event group for
        //continuing add new events
        if (Input::get('event_group_id') == '') {
            $event_group_params = array('name' => Input::get('event_group_name'));
            $event_group = $this->tournamentEventGroupService->createEventGroup($event_group_params);
            $event_group_id = $event_group['id'];
            $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID($event_group_id);
            $start_time = '';
            $end_time = '';

        } else {
            $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID(Input::get('event_group_id'));

            $event_group_id = Input::get('event_group_id');
            $event_group_params = array('name' => Input::get('event_group_name'));
            $start_time = $new_created_event_group->start_date;
            $end_time = $new_created_event_group->end_date;

            //edit the tournament event group name
            $new_group_name = Input::get('event_group_name');
            $new_created_event_group->name = $new_group_name;
            $new_created_event_group->update();

        }

        $selected_events = Input::get('events');

        //if no event selected, just update tournament event group name
        if(Input::get('events') == '') {
            $selected_events = array();
        }

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

            //get event group type
            if($key == 0) {
                $group_type = $this->tournamentEventGroupService->getEventGroupTypeByEvent($selected_event);
            }
        }


        //get the earliest created event group by id to set the event group start date, and the latest event start date as
        //event group end date
//        $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID($event_group_id);
        $new_created_event_group->start_date = $start_time;
        $new_created_event_group->end_date = $end_time;
        $new_created_event_group->type = $group_type;
        $new_created_event_group->update();


        //if no events selected, do not create new events
        if(Input::get('events') != '') {

            //get all events that belong to this event group, send them to template
            $new_event_group_events = $this->tournamentEventGroupEventService->createEventGroupEvent($items);
        }

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
        $sports = $this->sportService->getAllSports();
        $event_list = $this->tournamentEventGroupService->getEventsByTournamentEventGroupToArray($id);

        return view('admin.tournaments.event-groups.edit')->with(['event_group' => $event_group,
                                                                  'sport_list' => $sports,
                                                                  'event_list' => $event_list,
                                                                  'event_group_id' => $id,
                                                                  'event_group_name' => $event_group->name]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $group_id
     * @return Response
     */
    public function update(Request $request, $group_id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($group_id)
    {
        $tournament_event_group = $this->tournamentEventGroupService->getEventGroupByID($group_id);
        $tournament_event_group->delete();
        $this->tournamentEventGroupEventService->removeAllEventsFromGroup($group_id);
        return redirect()->action('Admin\TournamentEventGroupController@index');
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

//        $events = $this->eventService->getAllEventsFromToday();

        $sports = $this->sportService->getAllSports();

//        $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID($group_id);

        $event_list = $this->tournamentEventGroupService->getEventsByTournamentEventGroupToArray($group_id);

        return view('admin.tournaments.event-groups.create')->with(['sport_list' => $sports,
                                                                    'event_group_name' => $group_name,
                                                                    'event_group_id' => $group_id,
                                                                    'event_list' => $event_list]);
    }


    /**
     * remove event from group
     * @param $group_id
     * @param $event_id
     * @param $group_name
     * @return $this
     */
    public function removeEventFromGroup($group_id, $event_id, $group_name) {

        $this->tournamentEventGroupEventService->removeEventFromGroup($group_id, $event_id);

//        $events = $this->eventService->getAllEventsFromToday();

        $sports = $this->sportService->getAllSports();

        $event_list = $this->tournamentEventGroupService->getEventsByTournamentEventGroupToArray($group_id);

        return view('admin.tournaments.event-groups.create')->with(['sport_list' => $sports,
                                                                    'event_group_name' => $group_name,
                                                                    'event_group_id' => $group_id,
                                                                    'event_list' => $event_list]);
    }


    /**
     * get all meetings
     * @return mixed
     */
    public function getAllMeetings() {
        return $this->meetingService->getAllMeetings();
    }

}
