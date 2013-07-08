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

		$changedSince = Input::get('changed_since', false);

		// default to show all todays races
		if (!$changedSince) {

			// store meetings & races in cache for 1 min at a time
			return \Cache::remember('meetings-' . $meetDate . $typeCode, 1, function() use (&$meetDate, &$typeCode) {

				$eachMeeting = $this -> getMeetingsAndRaces($meetDate, $typeCode);

				return array('success' => true, 'result' => $eachMeeting);

			});

		} else {

			//this is used for giving changes only with polling client side - temp solution until sockets inplemented

			// fetch all the meetings and races as per usual
			$eachMeeting = $this -> getMeetingsAndRaces($meetDate, $typeCode);

			// remove any meeting or race that has not updated
			// we need to keep a meeting if it has child races
			foreach ($eachMeeting as $meetingKey => $meeting) {

				// remove any races that are not updated after our changed since epoch time
				foreach ($meeting['races'] as $raceKey => $race) {

					$updatedAt = 0;

					if ($race['updated_at']) {

						$date = new \DateTime($race['updated_at']);
						$updatedAt = $date -> format('U');

					}

					if ($updatedAt < $changedSince) {

						unset($eachMeeting[$meetingKey]['races'][$raceKey]);

					}

				}

				// remove the races property if no races are updated
				if (count($eachMeeting[$meetingKey]['races']) == 0) {

					unset($eachMeeting[$meetingKey]['races']);

				}

				// remove any meetings that are not updated after our changed since epoch time
				$updatedAt = 0;

				if ($race['updated_at']) {

					$date = new \DateTime($meeting['updated_at']);
					$updatedAt = $date -> format('U');

				}

				if ($updatedAt < $changedSince) {

					unset($eachMeeting[$meetingKey]);

				}

			}

			return array('success' => true, 'result' => $eachMeeting);

		}

	}

	private function getMeetingsAndRaces($meetDate, $typeCode = 'r') {

		//fetch our meetings for the specified type i.e. r = racing, g = greyhouds, h = harness
		$events = TopBetta\RaceMeeting::whereRaw('start_date LIKE "' . $meetDate . '%" AND type_code = "' . $typeCode . '"') -> get();

		//TODO: make sure we have rows
		$meetingAndRaces = array();
		$eachMeeting = array();

		foreach ($events as $event) {

			$races = \TopBetta\RaceMeeting::getRacesForMeetingId($event -> id);

			$updatedAt = $event -> updated_at;
			if ($updatedAt -> year > 0) {

				$updatedAt = $updatedAt -> toISO8601String();

			} else {

				$updatedAt = false;

			}

			$meetingAndRaces = array('id' => (int)$event -> id, 'name' => $event -> name, 'meeting_grade' => $event -> meeting_grade, 'state' => $event -> state, 'weather' => ucwords(strtolower($event -> weather)), 'track' => ucwords(strtolower($event -> track)), 'updated_at' => $updatedAt, 'races' => $races);
			$eachMeeting[] = $meetingAndRaces;
		}

		return $eachMeeting;

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

			$meeting = array('id' => (int)$meetingDetails -> id, 'name' => $meetingDetails -> name, 'meeting_grade' => $meetingDetails -> meeting_grade, 'state' => $meetingDetails -> state, 'weather' => ucwords(strtolower($meetingDetails -> weather)), 'track' => ucwords(strtolower($meetingDetails -> track)), 'races' => ($races) ? \TopBetta\RaceMeeting::getRacesForMeetingId($meetingDetails -> id) : false);

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
