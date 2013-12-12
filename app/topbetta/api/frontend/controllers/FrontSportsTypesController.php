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
				
			}

			if (count($types) > 0) {

				// we need to type cast the strings to int
				foreach ($types as $type) {
	
					$eachType[] = array('id' => (int)$type -> id, 'market_type_id' => (int)$type->market_type_id, 'bet_type' => $type -> bet_type, 'status' => $type->status, 'line' => $type->line);
					
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
