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
		$data = \Cache::remember('meetings-' . $meetDate . $typeCode, 1, function() use (&$meetDate, &$typeCode) {

			//fetch our meetings for the specified type i.e. r = racing, g = greyhouds, h = harness
			$events = TopBetta\RaceMeeting::whereRaw('start_date LIKE "' . $meetDate . '%" AND type_code = "' . $typeCode . '"') -> get();

			//TODO: make sure we have rows
			$result['success'] = true;
			$meetingAndRaces = array();
			$eachMeeting = array();

			foreach ($events as $event) {
				$meetingRaces = array();

				$races = TopBetta\RaceMeeting::find($event -> id) -> raceevents;

				foreach ($races as $race) {

					$toGo = \TimeHelper::nicetime(strtotime($race -> start_date), 2);

					//convert the date to ISO 8601 format
					$startDatetime = new \DateTime($race -> start_date);
					$startDatetime = $startDatetime -> format('c');

					$meetingRaces[] = array("id" => $race -> id, "race_number" => $race -> number, "to_go" => $toGo, "start_datetime" => $startDatetime, "results" => false, "status" => $race -> status);

					$weather = $race -> weather;
					$track = $race -> track;
				}

				$meetingAndRaces = array('id' => $event -> id, 'name' => $event -> name, 'weather' => $weather, 'track' => $track, 'races' => $meetingRaces);
				$eachMeeting[] = $meetingAndRaces;
			}

			$result['result'] = $eachMeeting;

			return $result;

		});

		return $data;
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
