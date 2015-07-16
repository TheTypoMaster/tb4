<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontSportsEventsController extends Controller {

	public function nextToJump() {

		$limit = Input::get('limit', 10);
		// store next to jump in cache for 1 min at a time
		return \Cache::remember('nextToJump-sports-' . $limit, 1, function() use (&$limit) {

			$nextToJump = TopBetta\Models\SportsEvents::getNextEventsToJump($limit);

			$result = array();

			foreach ($nextToJump as $next) {

				$toGo = \TopBetta\Helpers\TimeHelper::nicetime(strtotime($next -> start_date), 2);

				//convert the date to ISO 8601 format
				$startDatetime = new \DateTime($next -> start_date);
				$startDatetime = $startDatetime -> format('c');

				$result[] = array('id' => (int)$next -> id, 'type' => $next -> sport_name, 'comp_id' => (int)$next -> comp_id, 'comp_name' => $next -> comp_name, 'name' => $next -> name, 'to_go' => $toGo, 'start_datetime' => $startDatetime);
			}

			return array('success' => true, 'result' => $result);

		});

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($compId = false, $tournamentFlag = false) {

		//special case to allow for events to be called directly with the comp id passed in
		$compId = Input::get('comp_id', $compId);
		$date = Input::get('date', null);
		$limit = Input::get('limit', null);

		// store sports events in cache for 10 min at a time
		return \Cache::remember('sportsEvents-' . $compId . $date . $limit . $tournamentFlag, 10, function() use (&$compId, &$date, &$limit, $tournamentFlag) {
			$sportsEvents = new TopBetta\Models\SportsEvents;
			$events = $sportsEvents -> getEvents($limit, (int)$compId, $date, $tournamentFlag);

			//var_dump(\DB::getQueryLog());

			$result = array();

			foreach ($events as $event) {

				//convert the date to ISO 8601 format
				$startDatetime = new \DateTime($event -> event_start_time);
				$startDatetime = $startDatetime -> format('c');

				$result[] = array('id' => (int)$event -> id, 'name' => $event -> event_name, 'start_time' => $startDatetime, 'ext_event_id' => (int)$event -> ext_event_id);

			}

			return array('success' => true, 'result' => $result);
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
