<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontSportsTypesOptionsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($compId = false, $eventId = false) {

		//special case to allow for types & options to be called directly with the event id passed in
		$eventId = Input::get('event_id', $eventId);

		// store sports types & options in cache for 10 min at a time
		$data = \Cache::remember('sportsTypesOptions-' . $eventId, 10, function() use (&$eventId) {
			$sportsOptions = new TopBetta\SportsTypesOptions;
			$options = $sportsOptions -> getTypesAndOptions($eventId);

			//var_dump(\DB::getQueryLog());

			$ret = array();
			$ret['success'] = true;

			$result = array();

			foreach ($options as $option) {

				$result[] = array('id' => $option -> selection_id, 'bet_type' => $option -> bet_type, 'bet_selection' => $option -> bet_selection, 'odds' => $option -> odds, 'bet_place_ref' => $option -> bet_place_ref, 'bet_type_ref' => $option -> bet_type_ref, 'external_selection_id' => $option -> external_selection_id);

			}

			$ret['result'] = $result;

			return $ret;
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
