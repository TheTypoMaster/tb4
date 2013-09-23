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
	public function index($compId = false, $eventId = false, $tourn = false) {

		//tournaments require types matched to comps - front side tells us what they want
		$tournamentFlag = Input::get('tournament', $tourn);

		if ($tournamentFlag) {

			$getType = 'tourn';
			$eventCompId = Input::get('comp_id', $compId);

		} else {

			$getType = 'live';
			$eventCompId = Input::get('event_id', $eventId);

		}

		// store sports types in cache for 10 min at a time
		return \Cache::remember('sportsTypes-' . $eventCompId . '-' .$eventId. '-' . $getType, 10, function() use ($eventCompId, $tournamentFlag, $eventId) {

			$sportsTypes = new TopBetta\SportsTypes;

			if (!$tournamentFlag) {

				$types = $sportsTypes -> getTypes($eventCompId);

			} else {

				$types = $sportsTypes -> getTournamentTypes($eventCompId, $eventId);
				$betTypes = count($types);
				
				// TODO: This should be moved to a more maintainable solution for tournaments only
				// if we have some bet types
				if($betTypes > 0){
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
					($betTypes > 9) ? $betLimitValue = $offer_market_limit[9] : $betLimitValue = $offer_market_limit[$betTypes];
				}
			}

			if (count($types) > 0) {

				// we need to type cast the strings to int
				foreach ($types as $type) {
	
					if(!$tournamentFlag){
						$eachType[] = array('id' => (int)$type -> id, 'bet_type' => $type -> bet_type, 'status' => $type->status);
					}else{
						// Add a bet_limit field
						$eachType[] = array('id' => (int)$type -> id, 'bet_type' => $type -> bet_type, 'status' => $type->status, 'bet_limit' => $betLimitValue);
					}
				}

				return array('success' => true, 'result' => $eachType);

			} else {

				// we need to return an empty result for the front end :-)
				return array('success' => true, 'result' => array());

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
