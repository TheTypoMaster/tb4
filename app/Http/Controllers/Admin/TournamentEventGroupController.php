<?php

namespace TopBetta\Http\Controllers\Admin;

use Carbon\Carbon;
//use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Sports\SportsService;
use TopBetta\Services\Tournaments\TournamentEventGroupEventService;
use TopBetta\Services\Tournaments\TournamentEventGroupService;
use TopBetta\Services\Events\CompetitionService;
use Input;
//use Request;
use Illuminate\Http\Request;
use TopBetta\Services\Tournaments\TournamentEventGroupSportService;

class TournamentEventGroupController extends Controller
{

    public function __construct(TournamentEventGroupService $tournamentEventGroupService, TournamentEventGroupEventService $tournamentEventGroupEventService,
                                EventService $eventService,
                                SportsService $sportService,
                                MeetingService $meetingService,
                                CompetitionService $competitionService,
                                TournamentEventGroupSportService $tournamentEventGroupSportService)
    {

        $this->tournamentEventGroupService = $tournamentEventGroupService;
        $this->tournamentEventGroupEventService = $tournamentEventGroupEventService;
        $this->eventService = $eventService;
        $this->sportService = $sportService;
        $this->meetingService = $meetingService;
        $this->competitionService = $competitionService;
        $this->tournamentEventGroupSportService = $tournamentEventGroupSportService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(Input::get('flag')) {
            $q = Input::get('search_username');
            $query = array('flag' => '1', 'search_username' => $q);
            $event_groups = $this->tournamentEventGroupService->searchEventGroups($q);
        } else {
            $query = array();
            $event_groups = $this->tournamentEventGroupService->getAllEventGroups();
        }

        return view('admin.tournaments.event-groups.index')->with(['event_groups' => $event_groups, 'query' => $query]);
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

        //check if the form is for existing meeting or future meeting
        if(Input::get('flag') == 'existing_meeting') {

            $this->validate($request, ['event_group_name' => 'required|max:255',
                                       'events' => 'required']);

            //if event_group_id is empty, create new event_group, otherwise use the already created event group for
            //continuing add new events
            if (Input::get('event_group_id') == '') {
                $event_group_params = array('name' => Input::get('event_group_name'));
                $event_group = $this->tournamentEventGroupService->createEventGroup($event_group_params); //$event_group->sports()->attach()
                $event_group_id = $event_group['id'];
                $new_created_event_group = $this->tournamentEventGroupService->getEventGroupByID($event_group_id);
                $start_time = '';
                $end_time = '';

                //create relationship between tournament event group and sport
                $sport_id = Input::get('sports');
//                $tour_event_group_sport_data = array('tournament_event_group_id' => $event_group_id, 'sport_id' => $sport_id);
                $tour_event_group_sport = $new_created_event_group->sports()->attach($sport_id);
//                $tour_event_group_sport = $this->tournamentEventGroupSportService->createTourEventGroupSport($tour_event_group_sport_data);


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

                //create data array for getting relationship from pivot table
                $sport_id = Input::get('sports');
                $tour_event_group_sport_data = array('tournament_event_group_id' => $new_created_event_group->id, 'sport_id' => $sport_id);

                //check if the relationship in pivot table, if not, create a new one
                $tour_event_group_sport = $this->tournamentEventGroupSportService->getTourEventGroupSport($tour_event_group_sport_data);
                if($tour_event_group_sport == null) {

                    //create relationship between tournament event group and sport
                    $tour_event_group_sport = $new_created_event_group->sports()->attach($sport_id);
                }

            }

            $selected_events = Input::get('events');

            //if no event selected, just update tournament event group name
            if (Input::get('events') == '') {
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
                if ($key == 0) {
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
            if (Input::get('events') != '') {

                //get all events that belong to this event group, send them to template
                $new_event_group_events = $this->tournamentEventGroupEventService->createEventGroupEvent($items);
            }

            return redirect()->action('Admin\TournamentEventGroupController@keepAdding', ['event_group_name' => $event_group_params['name'],
                'event_group_id' => $event_group_id]);
        } else {

            $this->validate($request, ['event_group_name' => 'required|max:255',
                                       'meeting_date' => 'required']);

            $event_group_name = Input::get('event_group_name');
            $sport_id = Input::get('races');
            $venue_id = Input::get('meeting');
            $start_date = Input::get('meeting_date');
            $start_date = Carbon::createFromFormat('Y-m-d H:i', $start_date);
//            dd(Carbon::now());
            if($sport_id == 1) {
                $tournament_competition_id = 31;
                $type_code = 'R';
            } else if ($sport_id == 2) {
                $tournament_competition_id = 32;
                $type_code = 'H';
            } else if ($sport_id == 3) {
                $tournament_competition_id = 33;
                $type_code = 'G';
            }

            //if event_group_id exists, the form comes from edit page, otherwise the form comes from create page
            if(Input::get('event_group_id')) {
                $event_group = $this->tournamentEventGroupService->getEventGroupByID(Input::get('event_group_id'));
                $event_group->name = $event_group_name;
                $event_group->update();

                $competition_id = Input::get('competition_id');
                $competition = $this->competitionService->updateCompetitionFromMeetingVenue($competition_id, $type_code, $sport_id, $tournament_competition_id, $venue_id, $start_date);

//                $competition = $this->competitionService->getCompetitionById(Input::get('competition_id'));
////                $competition->venue_id = $venue_id;
//                $competition->start_date = $start_date->toDateString();
//                $competition->tournament_competition_id = $tournament_competition_id;
//                $competition->type_code = $type_code;
//                $competition->update();

//                $competition_id = Input::get('event_group_id');
            } else {
                $competition = $this->competitionService->createCompetitionFromMeetingVenue($sport_id, $type_code, $tournament_competition_id, $venue_id, $start_date);
                $competition_id = $competition['id'];

                $data = array('name' => Input::get('event_group_name'), 'type' => 'race', 'event_group_id' => $competition_id, 'start_date' => $start_date, 'end_date' => $start_date);
                $event_group = $this->tournamentEventGroupService->createEventGroup($data);

                //create relationship between tournament event group and sport
                $sport_id = Input::get('sports');
//                $tour_event_group_sport_data = array('tournament_event_group_id' => $event_group_id, 'sport_id' => $sport_id);
                $tour_event_group_sport = $event_group->sports()->attach($sport_id);
//                $tour_event_group_sport = $this->tournamentEventGroupSportService->createTourEventGroupSport($tour_event_group_sport_data);
            }


            return redirect()->action('Admin\TournamentEventGroupController@index');
        }
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

        $future_meeting_id = '';
        $flag = 'existing_meeting';
        //if event group has event group id, then it is future meeting
        if($event_group->event_group_id) {
            $flag = 'future_meeting';
            $future_meeting_id = $event_group->event_group_id;
        }

        return view('admin.tournaments.event-groups.edit')->with(['event_group' => $event_group,
                                                                  'sport_list' => $sports,
                                                                  'event_list' => $event_list,
                                                                  'event_group_id' => $id,
                                                                  'event_group_name' => $event_group->name,
                                                                  'flag' => $flag,
                                                                  'future_meeting_id' => $future_meeting_id]);
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

        //delete relationship between tournament event group and sport
//        $this->tournamentEventGroupSportService->

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
