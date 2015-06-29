<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use App;
use TopBetta\Models\TournamentSport;
use TopBetta\Models\SportsEvents;

class FrontCombinedSportsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($compId = null, $tournamentFlag = false)
    {

        
        $sportsList = TournamentSport::getActiveSports()->toArray();

        // if they pass in a sport id, show next event
        if ($sportId = \Input::get('sport_id')) {
                $nextSport = SportsEvents::getNextEventsToJump(1, $sportId);

                if (count($nextSport) == 0) {
                  return array("success" => false, "error" => "No Next To Jump Sports Event");			
                } 
                $compId = $nextSport[0]->comp_id;
                $eventId = \TopBetta\Models\RaceEvent::where('external_event_id',$nextSport[0]->external_event_id)->value('id');
        }

        $compId = \Input::get('comp', $compId);

        if (!$compId) {
            // return the next to jump event
            $nextToJump = SportsEvents::getNextEventsToJump(1);
            if (count($nextToJump) == 0) {
                return array("success" => false, "error" => "No Next To Jump Sports Event");			
            } 
            $compId = $nextToJump[0]->comp_id;
            $eventId = \TopBetta\Models\RaceEvent::where('external_event_id',$nextToJump[0]->external_event_id)->value('id');
        }

        // SPORTS & COMP
        $request = \Request::create("/api/v1/sports/$compId", 'GET', array("tournamentFlag" => $tournamentFlag));
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


        $sportsEventsController = new FrontSportsEventsController;
        $events = $sportsEventsController->index($compId, $tournamentFlag);

        /*
        $request = \Request::create("/api/v1/sports/$compId/events", 'GET');
        $response = \Route::dispatch($request);

        $events = $response->getOriginalContent();
        */

        if (!$events['success'] || count($events['result']) < 1) {
            // return array("success" => false, "error" => "No events available");
            return array('success' => true, 'result' => array('sports_list' => $sportsList, 'sport' => $sport, 'competition' => $comp, 'events' => array(), 'types' => array(), 'options' => array(), 'selected' => array('comp_id' => (int)$compId, 'event_id' => false, 'type_id' => false)));
        }

        $events = $events['result'];
        $allEvents = array();

        $nextEvent = false;

        foreach ($events as $key => $value) {
            $events[$key]['comp_id'] = (int)$compId;
            $allEvents[] = (int)$value['id'];

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

        $typesController = new FrontSportsTypesController();
        $types = $typesController->index($compId, $eventId, $tournamentFlag);

        // $request = \Request::create("/api/v1/sports/$compId/events/$eventId/types", 'GET');

        // $response = \Route::dispatch($request);

        // $types = $response->getOriginalContent();

        if (!$types['success'] || count($types['result']) == 0) {
            // return array("success" => false, "error" => "No types available");
            return array('success' => true, 'result' => array('sports_list' => $sportsList, 'sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => array(), 'options' => array(), 'selected' => array('comp_id' => (int)$compId, 'event_id' => (int)$eventId, 'type_id' => false)));
        }

        $types = $types['result'];

        foreach ($types as $key => $value) {
            $types[$key]['event_id'] = (int)$eventId;
        }

        // OPTIONS
        
        // Did they pass in a market type id? 
        // - this means they want all options for all events for this market type
        if ($marketTypeId = \Input::get('market_type_id')) {
        	$optionsController = App::make('TopBetta\Http\Controllers\Frontend\FrontSportsOptionsController');
        	$options = $optionsController->getAllOptionsForMarketTypeId($allEvents, $marketTypeId);

        	$typeId = $marketTypeId;
        	$oldTypeId = \Input::get('type_id');

        } else {
			$typeId = \Input::get('type_id', $types[0]['id']);

			$request = \Request::create("/api/v1/sports/$compId/events/$eventId/types/$typeId/options", 'GET');

			$response = \Route::dispatch($request);

			$options = $response->getOriginalContent();
        }
        
		// This is for front-end madness!!
        $selectedTypeId = isset($oldTypeId) ? $oldTypeId : $typeId;

        if (!$options['success']) {
            // return array("success" => false, "error" => "No options available");
            return array('success' => true, 'result' => array('sports_list' => $sportsList, 'sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => $types, 'options' => array(), 'selected' => array('comp_id' => (int)$compId, 'event_id' => (int)$eventId, 'type_id' => (int)$typeId)));
        }

        $options = $options['result'];

// This may not be needed now
//         foreach ($options as $key => $value) {
//             $options[$key]['type_id'] = (int)$typeId;
//         }

                return array('success' => true, 'result' => array('sports_list' => $sportsList, 'sport' => $sport, 'competition' => $comp, 'events' => $events, 'types' => $types, 'options' => $options, 'selected' => array('comp_id' => (int)$compId, 'event_id' => (int)$eventId, 'type_id' => (int)$selectedTypeId, 'market_type_id' => (int)$typeId)));
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