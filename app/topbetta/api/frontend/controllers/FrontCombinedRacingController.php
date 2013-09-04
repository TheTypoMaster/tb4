<?php
namespace TopBetta\frontend;

class FrontCombinedRacingController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($type = 'r', $race = false)
    {

        // required input
        $typeCode = \Input::get('type', $type);
        $raceId = \Input::get('race', $race);

        if (!$raceId) {

            return array("success" => false, "error" => "No race selected");

        }

        $request = \Request::create('/api/v1/racing/meetings?type=' . $typeCode, 'GET');

        $response = \Route::dispatch($request);

        $meetingsAndRaces = $response->getOriginalContent();

        if (!$meetingsAndRaces['success']) {
            return array("success" => false, "error" => "No meetings and races available");
        }

        $meetingsAndRaces = $meetingsAndRaces['result'];

        $races = array();

        foreach ($meetingsAndRaces as $id => $meeting) {

            $race = $meetingsAndRaces[$id]['races'];

            foreach ($race as $key => $value) {
                $race[$key]['meeting_id'] = $meeting['id'];
            }

            $races = array_merge($races, $race);

            unset($meetingsAndRaces[$id]['races']);
        }

        $runnersController = new FrontRunnersController();
        $runners = $runnersController->index(false, $raceId);


        if (!$runners['success']) {
            return array("success" => false, "error" => "No runners available");
        }

        $runners = $runners['result'];

        foreach ($runners as $key => $value) {
            $runners[$key]['race_id'] = (int)$raceId;
        }

        return array('success' => true, 'result' => array('meetings' => $meetingsAndRaces, 'races' => $races, 'runners' => $runners));

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