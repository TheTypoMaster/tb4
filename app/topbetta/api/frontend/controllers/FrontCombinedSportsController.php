<?php
namespace TopBetta\frontend;

class FrontCombinedSportsController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($compId = null)
    {

        $compId = \Input::get('comp', $compId);

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
            // return array("success" => false, "error" => "No events available");
            return array('success' => true, 'result' => array('sport' => $sport, 'competition' => $comp, 'events' => array(), 'types' => array(), 'options' => array(), 'selected' => array('event_id' => false, 'type_id' => false)));
        }

        $events = $events['result'];

        $nextEvent = false;

        foreach ($events as $key => $value) {
            $events[$key]['comp_id'] = (int)$compId;

            // we need to determine the next event to jump
            $startTime = new \DateTime($value['start_time']);
            $startTime = $startTime -> format('U');

            $nowTime = new \DateTime();
            $nowTime = $nowTime -> format('U');

            if ($startTime >= $nowTime && !$nextEvent) {
                $nextEvent = $value['id'];
            }
        }

        // if we don't have a future event, select the first event
        if (!$nextEvent) {
            $nextEvent = $events[0]['id'];
        }

        // BET TYPES
        $eventId = \Input::get('event_id', $nextEvent);

        $request = \Request::create("/api/v1/sports/$compId/events/$eventId/types", 'GET');

        $response = \Route::dispatch($request);

        $types = $response->getOriginalContent();

        if (!$types['success']) {
            // return array("success" => false, "error" => "No types available");
            return array('success' => true, 'result' => array('sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => $types, 'options' => $options, 'selected' => array('event_id' => (int)$eventId, 'type_id' => false)));
        }

        $types = $types['result'];

        foreach ($types as $key => $value) {
            $types[$key]['event_id'] = (int)$eventId;
        }

        // OPTIONS
        $typeId = \Input::get('type_id', $types[0]['id']);

        $request = \Request::create("/api/v1/sports/$compId/events/$eventId/types/$typeId/options", 'GET');

        $response = \Route::dispatch($request);

        $options = $response->getOriginalContent();

        if (!$options['success']) {
            // return array("success" => false, "error" => "No options available");
            return array('success' => true, 'result' => array('sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => $types, 'options' => array(), 'selected' => array('event_id' => (int)$eventId, 'type_id' => (int)$typeId)));
        }

        $options = $options['result'];

        foreach ($options as $key => $value) {
            $options[$key]['type_id'] = (int)$typeId;
        }

        return array('success' => true, 'result' => array('sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => $types, 'options' => $options, 'selected' => array('event_id' => (int)$eventId, 'type_id' => (int)$typeId)));
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