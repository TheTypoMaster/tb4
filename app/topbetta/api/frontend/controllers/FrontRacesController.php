<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontRacesController extends \BaseController {

	public function nextToJump() {

		$limit = Input::get('limit', 10);

		// store next to jump in cache for 1 min at a time
		$data = \Cache::remember('nextToJump-' . $limit, 1, function() use (&$limit) {

			$nextToJump = TopBetta\RaceEvent::nextToJump($limit);
			//return $nextToJump;

			$ret = array();
			$ret['success'] = true;

			$result = array();

			foreach ($nextToJump as $next) {

				$toGo = \TimeHelper::nicetime(strtotime($next -> start_date), 2);

				//convert the date to ISO 8601 format
				$startDatetime = new \DateTime($next -> start_date);
				$startDatetime = $startDatetime -> format('c');

				$result[] = array('type' => $next -> type, 'meeting_id' => $next -> meeting_id, 'meeting_name' => $next -> meeting_name, 'race_number' => $next -> number, 'to_go' => $toGo, 'start_datetime' => $startDatetime, 'distance' => $next -> distance);
			}

			$ret['result'] = $result;

			return $ret;

		});

		return $data;

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($meetingId) {
		//
		return "Getting races for meeting id: $meetingId";
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
