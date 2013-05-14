<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontRunnersController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($meetingId = false, $raceId = false) {
			
		//special case to allow for runners to be called directly with the race id passed in
		$raceId = Input::get('race', $raceId);

		// store runners in cache for 1 min at a time
		$data = \Cache::remember('runners-' . $raceId, 1, function() use (&$raceId) {
			$runners = TopBetta\RaceSelection::getRunnersForRaceId($raceId);
			//return $nextToJump;

			$ret = array();
			$ret['success'] = true;

			$result = array();

			foreach ($runners as $runner) {
				$scratched = ($runner -> status == "Scratched") ? true : false;
				$pricing = array('win' => number_format($runner -> win_odds, 2), 'place' => number_format($runner -> place_odds, 2));

				$result[] = array('id' => $runner -> id, 'name' => $runner -> name, 'jockey' => $runner -> associate, 'trainer' => $runner -> associate, 'weight' => $runner -> weight, 'saddle' => $runner -> number, 'barrier' => $runner -> barrier, 'scratched' => $scratched, 'form' => false, 'pricing' => $pricing, 'risa_silk_id' => $runner -> silk_id);

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
