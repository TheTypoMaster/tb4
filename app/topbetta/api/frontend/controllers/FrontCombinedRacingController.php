<?php
namespace TopBetta\frontend;

class FrontCombinedRacingController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($type = 'r', $race = false, $meeting = false)
    {

        // required input
        $typeCode = \Input::get('type', $type);
        $raceId = \Input::get('race', $race);
        $meetingId = \Input::get('meeting', $meeting);

        if (!$meetingId && !$raceId) {
            return array("success" => false, "error" => "No meeting id or race id selected");
        }

        // work out meeting id based off race id only
        if (!$meetingId && $raceId) {
            $meetingId = \TopBetta\RaceEventGroupEvent::where('event_id', $raceId)->pluck('event_group_id');
        }

        $meetingsController = new FrontMeetingsController();
        $meetingAndRaces = $meetingsController->show($meetingId, true);

        if (!$meetingAndRaces['success']) {
            return array("success" => false, "error" => "No meetings and races available");
        }

        $meetingAndRaces = $meetingAndRaces['result'];

        $races = $meetingAndRaces['races'];

        if ($races) {
            foreach ($races as $key => $value) {
                $races[$key]['meeting_id'] = $meetingAndRaces['id'];
            }
        }

        unset($meetingAndRaces['races']);

        $meeting = $meetingAndRaces;

        $runnersController = new FrontRunnersController();
        $runners = $runnersController->index(false, $raceId);


        if (!$runners['success']) {
            return array("success" => false, "error" => "No runners available");
        }

        $runners = $runners['result'];

        foreach ($runners as $key => $value) {
            $runners[$key]['race_id'] = (int)$raceId;
            
            // add runners form and last starts
            $runnersForm = \TopBetta\RisaForm::with('lastStarts')->where('runner_code', $runners[$key]['runner_code'])->get();
            
            $runners[$key]['detailed_form'] = array ('age' => $runnersForm[0]->age, 'colour' => $runnersForm[0]->colour, 'sex' => $runnersForm[0]->sex, 'career' => $runnersForm[0]->career_results, 
            							'track' => $runnersForm[0]->track_results, 'track_distance' => $runnersForm[0]->track_distance_results, 'first_up' => $runnersForm[0]->first_up_results, 'second_up' => $runnersForm[0]->second_up_results,
            							'good' => $runnersForm[0]->good_results, 'dead' => $runnersForm[0]->dead_results, 'slow' => $runnersForm[0]->slow_results, 'heavy' => $runnersForm[0]->heavy_results);
            
            foreach ($runnersForm[0]->last_starts as $last_starts){
            	$runners[$key]['detailed_form']['last_starts'][] = array('id' => $last_starts->id, 'finish_position' => $last_starts->finish_position, 'race_starters' => $last_starts->race_starters, 'abr_venue' => $last_starts->abr_venue, 'race_distance' => $last_starts->race_distance,
            						'name_race_form' => $last_starts->name_race_form, 'mgt_date' => $last_starts->mgt_date, 'track_condition' => $last_starts->track_condition, 'numberic_rating' => $last_starts->numberic_rating, 'jockey_initials' => $last_starts->jockey_initials,
            						'jockey_surname' => $last_starts->jockey_surname, 'handicap' => $last_starts->handicap, 'barrier' => $last_starts->barrier, 'starting_win_price' => $last_starts->starting_win_price, 'other_runner_name' => $last_starts->other_runner_name,
            						'other_runner_barrier' => $last_starts->other_runner_barrier, 'in_running_800' => $last_starts->in_running_800, 'in_running_400' => $last_starts->in_running_400, 'other_runner_time' => $last_starts->other_runner_time, 'margin_decimal' => $last_starts->margin_decimal);
            }
            
            
            //$runners[$key]['detailed_form'] = $runnersForm[0];
           
        }

        return array('success' => true, 'result' => array('meeting' => $meeting, 'races' => $races, 'runners' => $runners));

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
     * @param  int  $id
     * @return Response
     */
    public function update($id)
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