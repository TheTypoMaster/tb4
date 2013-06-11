<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//
		$type = Input::get('type', 'racing');
		
		//sub type e.g. for racing: greyhounds, for sports: nrl
		$sub_type = Input::get('sub_type', null);

		// store sports and comps in cache for 10 min at a time
		$data = \Cache::remember('tournaments-' . $type . '-' . $sub_type, 1, function() use (&$type, &$sub_type) {
			$tournamentModel = new \TopBetta\Tournament;

			$list_params = array('jackpot' => false, 'private' => 0, 'type' => $type, 'sub_type' => $sub_type);
			$tournamentList = $tournamentModel -> getTournamentActiveList($list_params);

			$ret = array();
			$ret['success'] = true;

			$result = array();

			$meetingId = NULL;
			$eachMeeting = array();

			foreach ($tournamentList as $tournament) {

				$numTournaments = 0;

				if ($meetingId != $tournament -> event_group_id) {

					$meetingName = $tournament -> event_group_name;
					$meetingId = $tournament -> event_group_id;

					$tourns = array();

					foreach ($tournamentList as $tourn) {

						if ($tourn -> event_group_name == $meetingName) {

							$numTournaments++;

							//calculate prize pool
							$prizePool = $tournamentModel -> calculateTournamentPrizePool($tourn -> id);

							//fetch num entries
							$numEntries = \TopBetta\TournamentTicket::countTournamentEntrants($tourn -> id);

							//TODO: fetch places paid
							//this requires several tournament models to be implemented first
							//Tournament, TournamentPlacesPaid, TournamentLeaderboard

							$placesPaid = $tournamentModel -> calculateTournamentPlacesPaid($tourn, $numEntries, $prizePool);

							//convert the date to ISO 8601 format
							$startDatetime = new \DateTime($tourn -> start_date);
							$startDatetime = $startDatetime -> format('c');

							$tourns[] = array('id' => (int)$tourn -> id, 'buy_in' => (int)$tourn -> buy_in, 'entry_fee' => (int)$tourn -> entry_fee, 'num_entries' => (int)$numEntries, 'prize_pool' => (int)$prizePool, 'places_paid' => (int)$placesPaid, 'start_date' => $startDatetime);
						}

					}
					$eachMeeting[] = array('id' => (int)$meetingId, 'name' => $meetingName, 'state' => $tournament -> state, 'num_tournaments' => $numTournaments, 'sub_type' => $tournament -> sport_name, 'tournaments' => $tourns);
				}
			}

			$ret['result'] = $eachMeeting;

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
