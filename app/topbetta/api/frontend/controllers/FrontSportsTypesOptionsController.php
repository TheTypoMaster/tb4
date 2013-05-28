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
		return \Cache::remember('sportsTypesOptions-' . $eventId, 10, function() use (&$eventId) {
			$sportsOptions = new TopBetta\SportsTypesOptions;
			$types = $sportsOptions -> getTypesAndOptions($eventId);

			//var_dump(\DB::getQueryLog());

			$typeId = 0;

			// Group the types with options which come through as one list
			foreach ($types as $type) {

				if ($typeId != $type -> type_id) {
					$typeId = $type -> type_id;

					foreach ($types as $option) {

						if ($option -> type_id == $typeId) {

							$options[] = array('id' => (int)$option -> selection_id, 'bet_selection' => $option -> bet_selection, 'odds' => (int)$option -> odds, 'bet_place_ref' => (int)$option -> bet_place_ref, 'bet_type_ref' => $option -> bet_type_ref, 'external_selection_id' => (int)$option -> external_selection_id);
						}
					}

					// our single type parent with all children options
					$eachType[] = array('id' => (int)$typeId, 'name' => $type -> bet_type, 'options' => $options);
				}
			}

			return array('status' => true, 'result' => $eachType);

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
