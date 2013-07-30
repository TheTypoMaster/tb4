<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontSportsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		$date = Input::get('date', null);
		$sid = Input::get('sport_id', null);

		// store sports and comps in cache for 10 min at a time
		return \Cache::remember('sportsComps-', 10, function() use (&$date, &$sid) {
			$sportsComps = new TopBetta\SportsComps;
			$sports = $sportsComps -> getSportAndComps($date, $sid);

			//var_dump(\DB::getQueryLog());

			$sportName = '';
			$eachSport = array();
			
			// Group the sports and comps which come through as one list
			foreach ($sports as $sport) {

				if ($sportName != $sport -> sportName) {
					$sportName = $sport -> sportName;
					$sportId = $sport -> sportID;
					$comps = array();
					foreach ($sports as $comp) {

						if ($comp -> sportName == $sportName) {

							//convert the date to ISO 8601 format
							$startDatetime = new \DateTime($comp -> start_date);
							$startDatetime = $startDatetime -> format('c');

							$comps[] = array('id' => (int)$comp -> eventGroupId, 'name' => $comp -> name, 'start_date' => $startDatetime);
						}

					}
					$eachSport[] = array('id' => (int)$sportId, 'name' => $sportName, 'competitions' => $comps);
				}
			}

			return array('success' => true, 'result' => $eachSport);
		});

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
		return "Sports create";
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
		//
		return "Sports store";
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		//
		return "Sports show $id";
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
		return "Sports edit $id";
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {
		//
		return "Sports update $id";
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
		return "Sports destroy $id";
	}

}
