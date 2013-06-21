<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontMeetingsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$result = array();

		$meetDate = Input::get('date', function() {
			$date = new \DateTime();
			return $date -> format('Y-m-d');
		});

		$typeCode = Input::get('type', 'r');

		// store meetings & races in cache for 1 min at a time		
		return \Cache::remember('meetings-' . $meetDate . $typeCode, 1, function() use (&$meetDate, &$typeCode) {

			//fetch our meetings for the specified type i.e. r = racing, g = greyhouds, h = harness
			$events = TopBetta\RaceMeeting::whereRaw('start_date LIKE "' . $meetDate . '%" AND type_code = "' . $typeCode . '"') -> get();

			//TODO: make sure we have rows
			$meetingAndRaces = array();
			$eachMeeting = array();

			foreach ($events as $event) {

				$races = \TopBetta\RaceMeeting::getRacesForMeetingId($event -> id);				

				$meetingAndRaces = array('id' => (int)$event -> id, 'name' => $event -> name, 'state' => $event -> state, 'weather' => ucwords(strtolower($event -> weather)), 'track' => ucwords(strtolower($event -> track)), 'races' => $races);
				$eachMeeting[] = $meetingAndRaces;
			}

			return array('success' => true, 'result' => $eachMeeting);

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
	public function show($id) {
		//
		$meetingDetails = TopBetta\RaceMeeting::find($id);
		
		if ($meetingDetails) {
				
			$races = Input::get('races', false);	
			
			$meeting = array('id' => (int)$meetingDetails -> id, 'name' => $meetingDetails -> name, 'state' => $meetingDetails -> state, 'weather' => ucwords(strtolower($meetingDetails -> weather)), 'track' => ucwords(strtolower($meetingDetails -> track)), 'races' => ($races) ? \TopBetta\RaceMeeting::getRacesForMeetingId($meetingDetails -> id) : false);
			
			return array('success' => true, 'result' => $meeting);			
			
		} else {
			
			return array('success' => false, 'error' => \Lang::get('racing.meeting_not_found'));
			
		}
		
		
		
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
