<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class FrontSportsOptionsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($compId = false, $eventId = false, $typeId = false) {

		//special case to allow for options to be called directly with the event id passed in
		$eventId = Input::get('event_id', $eventId);

		// store sports types in cache for 10 min at a time
		// return \Cache::remember('sportsOptions-' . $eventId . '-' . $typeId, .2, function() use (&$eventId, &$typeId) {

			$sportsOptions = new TopBetta\SportsOptions;
			$options = $sportsOptions -> getOptions($eventId, $typeId);

			$betOptions = count($options);
			
			// TODO: This should be moved to a more maintainable solution for tournaments only
			// if we have some bet options
			if($betOptions > 0){
				// Hard coded bet limits per number of markets linked to tournament
				$offer_market_limit = array(
						1 => 'unlimited',
						2 => 50000,
						3 => 50000,
						4 => 25000,
						5 => 25000,
						6 => 25000,
						7 => 25000,
						8 => 25000,
						9 => 10000,
				);
					
				// Set bet limit based on number of markets available to bet on
				($betOptions > 9) ? $betLimitValue = $offer_market_limit[9] : $betLimitValue = $offer_market_limit[$betOptions];
			}
				
			if (count($options) > 0) {

				//we need to type cast the strings to int
				foreach ($options as $option) {
					$eachOption[] = array('bet_selection' => $option -> bet_selection, 'odds' => (float)$option -> odds, 'bet_place_ref' => (int)$option -> bet_place_ref, 'external_selection_id' => (int)$option -> external_selection_id, 'id' => (int)$option -> selection_id, 'line' => $option -> line, 'bet_limit' => $betLimitValue, 'type_id' => (int)$option->type_id);
				}

				return array('success' => true, 'result' => $eachOption);

			} else {

				return array('success' => false, 'error' => Lang::get('sports.no_options'));

			}

		// });
	}
	
	public function getAllOptionsForMarketTypeId($allEvents, $marketTypeId) {
			$sportsOptions = new TopBetta\SportsOptions;
			$options = $sportsOptions -> getOptionsForMarketType($allEvents, $marketTypeId);	
// 			dd($options);
			$betOptions = count($options);
			
			// TODO: This should be moved to a more maintainable solution for tournaments only
			// if we have some bet options
			if($betOptions > 0){
				// Hard coded bet limits per number of markets linked to tournament
				$offer_market_limit = array(
						1 => 'unlimited',
						2 => 50000,
						3 => 50000,
						4 => 25000,
						5 => 25000,
						6 => 25000,
						7 => 25000,
						8 => 25000,
						9 => 10000,
				);
					
				// Set bet limit based on number of markets available to bet on
				($betOptions > 9) ? $betLimitValue = $offer_market_limit[9] : $betLimitValue = $offer_market_limit[$betOptions];
			}	
			
			if (count($options) > 0) {

				//we need to type cast the strings to int
				foreach ($options as $option) {
					$eachOption[] = array('bet_selection' => $option -> bet_selection, 'odds' => (float)$option -> odds, 'bet_place_ref' => (int)$option -> bet_place_ref, 'external_selection_id' => (int)$option -> external_selection_id, 'id' => (int)$option -> selection_id, 'line' => $option -> line, 'bet_limit' => $betLimitValue, 'type_id' => (int)$option->type_id);
				}

				return array('success' => true, 'result' => $eachOption);

			} else {

				return array('success' => false, 'error' => Lang::get('sports.no_options'));

			}			
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
