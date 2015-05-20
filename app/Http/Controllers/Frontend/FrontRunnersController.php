<?php namespace TopBetta\Http\Frontend\Controllers;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontRunnersController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($meetingId = false, $raceId = false) {

		$skipCache = Input::get('skip_cache', false);
		//special case to allow for runners to be called directly with the race id passed in
		$raceId = Input::get('race', $raceId);

		//TODO: make sure we have a race id

		if ($skipCache) {
			$runners = \TopBetta\RaceSelection::getRunnersForRaceId($raceId);
			return array('success' => true, 'result' => $runners);			
		}

		// store runners in cache for 1 min at a time
		return \Cache::remember('runners-' . $raceId, 1, function() use (&$raceId) {
			
			$runners = \TopBetta\RaceSelection::getRunnersForRaceId($raceId);

			return array('success' => true, 'result' => $runners);
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
