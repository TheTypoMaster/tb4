<?php
namespace TopBetta\Http\Frontend\Controllers;

use TopBetta;

class FrontCombinedTournamentsController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $tournId = \Input::get('tourn_id', null);
        $raceId = \Input::get('race', null);

        if (!$tournId) {

            return array("success" => false, "error" => "No tournament selected");

        }

        //$tournamentController = new FrontTournamentsController();
        $tournamentController = \App::make('\TopBetta\Http\Frontend\Controllers\FrontTournamentsController');

        $tournamentGrouped =  $tournamentController->show($tournId, true);

        if (!$tournamentGrouped['success']) {
            return array("success" => false, "error" => "No tournament grouped details available");
        }

        $tournamentGrouped = $tournamentGrouped['result'];

        $tournament = $tournamentGrouped['tournament'];

        unset($tournamentGrouped['tournament']);
        $group = $tournamentGrouped;

        $tournament['group_id'] = $group['id'];

        $tournamentDetails =  $tournamentController->show($tournId);

        if (!$tournamentDetails['success']) {
            return array("success" => false, "error" => "No tournament details available");
        }

        $tournamentDetails = $tournamentDetails['result'];
        $tournamentDetails['tournament_id'] = (int)$tournId;

        // tournament comments
        $tournamentCommentsController = new FrontTournamentsCommentsController();
        $tournamentComments = $tournamentCommentsController->index($tournId);

        if ($tournamentComments['success']) {
            $tournamentComments = $tournamentComments['result'];
        } else {
            $tournamentComments = $tournamentComments['error'];
        }    

        // used for combining data below
        $tournamentKey = array('group' => $group, 'tournament' => $tournament, 'details' => $tournamentDetails, 'comments' => $tournamentComments);
        $combinedResult = false;

        // RACING TOURNAMENT

        if ($tournamentDetails['tournament_type'] == 'r') {

            // work out next event for this meeting
            $meetingId = $tournamentDetails['meeting_id'];
            $races = Topbetta\RaceMeeting::getRacesForMeetingId($meetingId);

            $nextEvent = false;

            foreach ($races as $key => $value) {

                // we need to determine the next event to jump
                $startTime = new \DateTime($value['start_datetime']);
                $startTime = $startTime -> format('U');

                $nowTime = new \DateTime();
                $nowTime = $nowTime -> format('U');

                if ($startTime >= $nowTime && !$nextEvent) {
                    $nextEvent = (int)$value['id'];
                }
            }

            // if we don't have a future event, select the first event
            if (!$nextEvent && $races) {
                $nextEvent = (int)$races[0]['id'];
            }

            // if they passed in a race to select, lets keep that
            if ($raceId) {
                $nextEvent = (int)$raceId;
            }

            $racingController = \App::make('\TopBetta\Http\Frontend\Controllers\FrontCombinedRacingController');
            $racing =  $racingController->index('r', $nextEvent, $meetingId);

            if ($racing['success']) {

//                // add sponsor to race meeting name
//                $racing['result']['meeting']['name'] = $tournamentKey['group']['name'];

                $combinedResult = array_merge($tournamentKey, $racing['result'], array('selected' => array('race_id' => $nextEvent)));
            }

        } else {

            // SPORTS TOURNAMENT

            $sportsController = new FrontCombinedSportsController();
            $sports =  $sportsController->index($tournamentDetails['competition_id'], true);

            if ($sports['success']) {
                $combinedResult = array_merge($tournamentKey, $sports['result']);
            }

        }

        // we now have the tournament & sports or racing data combined
        if ($combinedResult) {
            return array('success' => true, 'result' => $combinedResult);
        } else {
            return array('success' => false, 'error' => 'Problem combining the data');
        }

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