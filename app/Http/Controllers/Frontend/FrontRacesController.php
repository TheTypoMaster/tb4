<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use TopBetta;
use Illuminate\Support\Facades\Input;
use TopBetta\Services\Caching\NextToJumpCacheService;

class FrontRacesController extends Controller {

    protected $nexttojumpcache;

    public function __construct(NextToJumpCacheService $nexttojumpcache){
        $this->nexttojumpcache = $nexttojumpcache;
    }

	public function nextToJump() {

		$limit = Input::get('limit', 10);

		// store next to jump in cache for 1 min at a time
		$data = \Cache::tags('topbetta-nexttojump')->rememberforever('topbetta-nexttojump', function()  {
            return $this->nexttojumpcache->getNextToJumpDBObject();
        });

        $ret = array();
        $ret['success'] = true;

        $result = array();

        foreach ($data as $next) {

            $toGo = \TopBetta\Helpers\TimeHelper::nicetime(strtotime($next['start_date']), 2);

            //convert the date to ISO 8601 format
            $startDatetime = new \DateTime($next['start_date']);
            $startDatetime = $startDatetime -> format('c');

            $result[] = array('id' => (int)$next['id'], 'type' => $next['type_code'], 'meeting_id' => (int)$next['meeting_id'], 'meeting_name' => $next['name'], 'state' => $next['state'], 'race_number' => (int)$next['number'], 'to_go' => $toGo, 'start_datetime' => $startDatetime);
        }

        $ret['result'] = $result;

        return $ret;

	}
	
	/**
	 * Get the next to jump event id's only
	 * 
	 * This is useful client side to know when an event is no longer selling
	 * as it's not present in this list anymore. Yes it's a roundabout way! :P
	 * 
	 * Only cached for about 10sec to be as close to jump time as practical.
	 * Ideally should only be called if events have just past on the frontend.
	 * 
	 * @return type
	 */
	public function nextToJumpEventIds() {
		$limit = Input::get('limit', 10);
		
		// just cache for 10sec, we need it as close to real time as we can
		return \Cache::remember('nextToJumpEventIds-' . $limit, 0.16, function() use ($limit) {
			// we only get events that are status selling
			$nextToJump = TopBetta\Models\RaceEvent::nextToJumpEventIds($limit);
			
			$ret = array();
			$ret['success'] = true;

			$result = array();
			
			foreach ($nextToJump as $event) {
				$result[] = (int)$event->id;
			}
			
			$ret['result'] = $result;

			return $ret;
		});
	}

	public function fastBetEvents() {
                
		$limit = Input::get('limit', 10);

		// store fast bet events in cache for 1 min at a time
		return \Cache::remember('fastBetEvents-' . $limit, 1, function() use ($limit) {

			// we only get events that are status selling
			$nextToJump = TopBetta\Models\RaceEvent::nextToJump($limit);

			$ret = array();
			$ret['success'] = true;

			$result = array();

			foreach ($nextToJump as $next) {

				$toGo = \TimeHelper::nicetime(strtotime($next -> start_date), 2);

				//convert the date to ISO 8601 format
				$startDatetime = new \DateTime($next -> start_date);
				$startDatetime = $startDatetime -> format('c');
                                
                                $runners = \TopBetta\Models\RaceSelection::getRunnersForRaceId($next->id);
                                $silk_base_url = "https://www.topbetta.com.au/silks/";

				$result[] = array('id' => (int)$next -> id, 'type' => $next -> type, 'meeting_id' => (int)$next -> meeting_id, 'meeting_name' => $next -> meeting_name, 'meeting_long_name' => $next->name, 'state' => $next -> state, 'race_number' => (int)$next -> number, 'to_go' => $toGo, 'status' => 'Selling', 'start_datetime' => $startDatetime, 'distance' => $next -> distance, 'silk_base_url' => $silk_base_url, 'runners' => $runners);
			}

			$ret['result'] = $result;

			return $ret;

		});

	}           
        
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($meetingId = false) {

		//special case to allow for race list to be called directly with the meeting id passed in
		$meetingId = Input::get('meeting_id', $meetingId);

		// store sports types & options in cache for 10 min at a time
		return \Cache::remember('racesForMeeting-' . $meetingId, 1, function() use (&$meetingId) {
				
			$races = \TopBetta\Models\RaceMeeting::getRacesForMeetingId($meetingId);

			return array('success' => true, 'result' => $races);
		});
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($meetingId, $id) {
				
		$race = TopBetta\Models\RaceEvent::find($id);
		if ($race) {
			//convert the date to ISO 8601 format
			$startDatetime = new \DateTime($race -> start_date);
			$startDatetime = $startDatetime -> format('c');	
			
			$result = array('id' => $race->id, 'external_race_id' => $race->external_event_id, 'race_number' => $race->number, 'name' => $race->name,
				'distance' => $race->distance, 'class' => $race->class, 'start_datetime' => $startDatetime, 'status' => $race->event_status_id);

			return array('success' => true, 'result' => $result);
		}

		return array('success' => false, 'error' => 'Race not found');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
	}

}
