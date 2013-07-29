<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontSportsTypesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($compId = false, $eventId = false) {

		//special case to allow for types to be called directly with the event id passed in
		$eventId = Input::get('event_id', $eventId);

		// store sports types in cache for 10 min at a time
		return \Cache::remember('sportsTypes-' . $eventId, 10, function() use (&$eventId) {

			$sportsTypes = new TopBetta\SportsTypes;
			$types = $sportsTypes -> getTypes($eventId);

			if (count($types) > 0) {

				//we need to type cast the strings to int
				foreach ($types as $type) {

					$eachType[] = array('id' => (int)$type -> id, 'bet_type' => $type -> bet_type);

				}

				return array('success' => true, 'result' => $eachType);

			} else {

				return array('success' => false, 'error' => \Lang::get('sports.no_types'));

			}

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
