<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;
use Log;

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

		// Get the all param if it exists and enforce boolean
		$all = Input::get('all', false);
		$all = $all == '1' ? true:false;

		$typeCode = Input::get('type', 'r');

		$changedSince = Input::get('changed_since', false);

		// default to show all todays races
		if (!$changedSince) {

			// If the all flag has been passed through in the URL, load from cache all of the meetings, otherwise filter
			// by meetings with the display flag
			if ($all) {

				return \Cache::remember('all-meetings-' . $meetDate . $typeCode, 1, function() use (&$meetDate, &$typeCode) {

					$eachMeeting = FrontMeetingsController::getMeetingsAndRaces($meetDate, $typeCode, false);

					return array('success' => true, 'result' => $eachMeeting);

				});
			}

			// store meetings & races in cache for 1 min at a time
			return \Cache::remember('meetings-' . $meetDate . $typeCode, 1, function() use (&$meetDate, &$typeCode) {

				$eachMeeting = FrontMeetingsController::getMeetingsAndRaces($meetDate, $typeCode);

				return array('success' => true, 'result' => $eachMeeting);

			});

		} else {

			//this is used for giving changes only with polling client side - temp solution until sockets inplemented

			// fetch all the meetings and races as per usual
			$eachMeeting = $this -> getMeetingsAndRaces($meetDate, $typeCode, $all);

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

	public static function getMeetingsAndRaces($meetDate, $typeCode = 'r', $displayOnly = true) {

		$query = TopBetta\RaceMeeting::select('tbdb_event_group.*')
			->join('tbdb_event_group_event AS ege', 'ege.event_group_id', '=', 'tbdb_event_group.id')
			->join('tbdb_event AS e', 'ege.event_id', '=', 'e.id')
			->where('e.start_date', 'like', $meetDate . '%')
			->where('tbdb_event_group.type_code', $typeCode);

		if ($displayOnly) {
			$query->where('tbdb_event_group.display_flag', 1)
				->where('e.display_flag', 1);
		}

		$query->groupBy('ege.event_group_id');

		// we want to include any events from meetings yesterday up until 6am the next day
		if (\Carbon\Carbon::now()->gte(\Carbon\Carbon::today()->addHours(6))) {
			$query->where('e.start_date', '>', $meetDate . ' 06:00');
		}
		
		$events = $query->get();

		//TODO: make sure we have rows
		$meetingAndRaces = array();
		$eachMeeting = array();

		foreach ($events as $event) {

			$races = \TopBetta\RaceMeeting::getRacesForMeetingId($event -> id, $displayOnly);

			$updatedAt = $event -> updated_at;
			if ($updatedAt -> year > 0) {
				$updatedAt = $updatedAt -> toISO8601String();
			} else {
				$updatedAt = false;
			}

			
			// grab the meeting start_date and format
			$startDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date);
			$startDateISO8601 = $startDate->toISO8601String();

			$meetingAndRaces = array('id' => (int)$event -> id, 'display' => $event->display_flag, 'name' => $event -> name, 'meeting_grade' => $event -> meeting_grade, 'state' => $event -> state, 'weather' => $event -> weather, 'track' => $event -> track, 'start_date' => $startDateISO8601, 'updated_at' => $updatedAt, 'races' => $races);
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
	 * @param  int $id
	 * @param bool $showRaces
	 * @return Response
	 */
	public function show($id, $showRaces = false) {
		// cache meeting query
		$meetingDetails = \Cache::remember("meeting-$id", 5, function() use ($id) {
			return \TopBetta\RaceMeeting::find($id);
		});

		if ($meetingDetails) {

			$races = Input::get('races', $showRaces);
			
			$startDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $meetingDetails->start_date);
			$startDateISO8601 = $startDate->toISO8601String();
			
			$meeting = array('id' => (int)$meetingDetails -> id, 'name' => $meetingDetails -> name, 'meeting_grade' => $meetingDetails -> meeting_grade, 'type_code' => $meetingDetails->type_code, 'country' => $meetingDetails->country, 'state' => $meetingDetails -> state, 'weather' => $meetingDetails -> weather, 'track' => $meetingDetails -> track, 'start_date' => $startDateISO8601, 'races' => ($races) ? \TopBetta\RaceMeeting::getRacesForMeetingId($meetingDetails -> id) : false);

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
