<?php
namespace TopBetta\frontend;

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

        if (!$tournId) {

            return array("success" => false, "error" => "No tournament selected");

        }

        $request = \Request::create("/api/v1/tournaments/$tournId", 'GET');

        $response = \Route::dispatch($request);

        $tournament = $response->getOriginalContent();

        if (!$tournament['success']) {
            return array("success" => false, "error" => "No tournament details available");
        }

        $tournament = $tournament['result'];

        // used for combining data below
        $tournamentKey = array('tournament' => $tournament);
        $combinedResult = false;

        // RACING TOURNAMENT

        if ($tournament['tournament_type'] == 'r') {

            // work out next event for this meeting
            $meetingId = $tournament['meeting_id'];
            $races = Topbetta\RaceMeeting::getRacesForMeetingId($meetingId);

            $nextEvent = false;

            foreach ($races as $key => $value) {

                // we need to determine the next event to jump
                $startTime = new \DateTime($value['start_datetime']);
                $startTime = $startTime -> format('U');

                $nowTime = new \DateTime();
                $nowTime = $nowTime -> format('U');

                if ($startTime >= $nowTime && !$nextEvent) {
                    $nextEvent = $value['id'];
                }
            }

            // if we don't have a future event, select the first event
            if (!$nextEvent) {
                $nextEvent = $races[0]['id'];
            }

            $racingController = new FrontCombinedRacingController();
            $racing =  $racingController->index('r', $nextEvent);

            if ($racing['success']) {
                $combinedResult = array_merge($tournamentKey, $racing['result'], array('selected' => array('race_id' => (int)$nextEvent)));
            }

        } else {

            // SPORTS TOURNAMENT

            $sportsController = new FrontCombinedSportsController();
            $sports =  $sportsController->index($tournament['competition_id']);

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