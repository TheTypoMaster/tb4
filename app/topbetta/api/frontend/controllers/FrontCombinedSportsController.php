<?php
namespace TopBetta\frontend;

class FrontCombinedSportsController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $compId = \Input::get('comp', null);

        if (!$compId) {
            return array("success" => false, "error" => "No comp id selected");
        }

        // SPORTS & COMP
        $request = \Request::create("/api/v1/sports/$compId", 'GET');
        $response = \Route::dispatch($request);

        $sportsComps = $response->getOriginalContent();

        if (!$sportsComps['success']) {
            return array("success" => false, "error" => "No comps available");
        }

        $sportsComps = $sportsComps['result'];

        // seperate comp from grouped results
        $comp = $sportsComps['competitions'];
        $comp['sport_id'] = (int)$sportsComps['id'];

        // seperate sport from comps
        unset($sportsComps['competitions']);
        $sport = $sportsComps;

        // EVENTS
        $request = \Request::create("/api/v1/sports/$compId/events", 'GET');
        $response = \Route::dispatch($request);

        $events = $response->getOriginalContent();

        if (!$events['success']) {
            return array("success" => false, "error" => "No events available");
        }

        $events = $events['result'];

        foreach ($events as $key => $value) {
            $events[$key]['comp_id'] = (int)$compId;
        }

        // BET TYPES
        $eventId = \Input::get('event_id', $events[0]['id']);

        $request = \Request::create("/api/v1/sports/$compId/events/$eventId/types", 'GET');

        $response = \Route::dispatch($request);

        $types = $response->getOriginalContent();

        if (!$types['success']) {
            return array("success" => false, "error" => "No types available");
        }

        $types = $types['result'];

        foreach ($types as $key => $value) {
            $types[$key]['event_id'] = $events[0]['id'];
        }

        // OPTIONS
        $typeId = \Input::get('type_id', $types[0]['id']);

        $request = \Request::create("/api/v1/sports/$compId/events/$eventId/types/$typeId/options", 'GET');

        $response = \Route::dispatch($request);

        $options = $response->getOriginalContent();

        if (!$options['success']) {
            return array("success" => false, "error" => "No options available");
        }

        $options = $options['result'];

        foreach ($options as $key => $value) {
            $options[$key]['type_id'] = $types[0]['id'];
        }

        return array('success' => true, 'result' => array('sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => $types, 'options' => $options));
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