<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsController extends \BaseController {

	protected $racingMap = array('r' => 'galloping', 'g' => 'greyhounds', 'h' => 'harness');

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

		//remap our subtype to the legacy keyword
		if ($sub_type) {

			if (array_key_exists($sub_type, $this -> racingMap)) {

				$sub_type = $this -> racingMap[$sub_type];

			}

		}

		//do we want a filtered list for the user joined tournaments only
		$entered = Input::get('entered', false);

		if ($entered) {

			// special case to clear my tournaments after a user has been logged in
			if (\Auth::guest()) return array("success" => true, "result" => array());

			//we want to see all entered tournaments for the entered list
			$type = null;

		}

		$tournamentModel = new \TopBetta\Tournament;

		$list_params = array('jackpot' => false, 'private' => 0, 'type' => $type, 'sub_type' => $sub_type);

		$tournamentList = $tournamentModel -> getTournamentActiveList($list_params);

		$filterList = false;

		if (\Auth::check() && $entered) {
			$filterList = $tournamentModel -> getMyTournamentListByUserID(\Auth::user() -> id);
		}

		$meetingId = NULL;
		$eachMeeting = array();

		// loop through every meeting
		foreach ($tournamentList as $tournament) {

			//TODO: bad code for now - had to live code on the server - can't get races to load on my f*n pc!!
			if ($filterList) {

				$keep = 0;
				foreach ($filterList as $myId) {

					if ($tournament -> id == $myId -> id) {
						$keep++;
					}
				}

				if ($keep == 0) {
					continue;
				}
			}

			$numTournaments = 0;

			if ($meetingId != $tournament -> event_group_id) {

				$meetingName = $tournament -> event_group_name;
				$meetingId = $tournament -> event_group_id;

				$tourns = array();

				// build our list of tournaments for this meeting
				foreach ($tournamentList as $tourn) {

					//TODO: bad code for now - had to live code on the server - can't get races to load on my f*n pc!!
					if ($filterList) {

						$keep = 0;
						foreach ($filterList as $myId) {

							if ($tourn -> id == $myId -> id) {
								$keep++;
							}
						}

						if ($keep == 0) {
							continue;
						}
					}

					if ($tourn -> event_group_id == $meetingId) {

						$numTournaments++;

						//calculate prize pool
						$prizePool = $tournamentModel -> calculateTournamentPrizePool($tourn -> id);

						//fetch num entries
						$numEntries = \TopBetta\TournamentTicket::countTournamentEntrants($tourn -> id);

						//fetch places paid
						$placesPaid = $tournamentModel -> calculateTournamentPlacesPaid($tourn, $numEntries, $prizePool);

						//convert the date to ISO 8601 format
						$startDatetime = \TimeHelper::isoDate($tourn -> start_date);
						$endDatetime = \TimeHelper::isoDate($tourn -> end_date);

						$tourns[] = array('id' => (int)$tourn -> id, 'buy_in' => (int)$tourn -> buy_in, 'entry_fee' => (int)$tourn -> entry_fee, 'num_entries' => (int)$numEntries, 'prize_pool' => (int)$prizePool, 'places_paid' => (int)$placesPaid, 'start_currency' => $tourn -> start_currency, 'start_date' => $startDatetime, 'end_date' => $endDatetime);
					}

					//handle sub_type for racing
					$flipRacingMap = array_flip($this -> racingMap);

					if (array_key_exists($tournament -> sport_name, $flipRacingMap)) {

						$sub_type_name = $flipRacingMap[$tournament -> sport_name];

					} else {

						$sub_type_name = $tournament -> sport_name;

					}

				}
				$eachMeeting[] = array('id' => (int)$meetingId, 'name' => $meetingName, 'state' => $tournament -> state, 'weather' => $tournament -> weather, 'track' => $tournament -> track, 'num_tournaments' => $numTournaments, 'sub_type' => $sub_type_name, 'tournaments' => $tourns);
			}
		}

		return array("success" => true, "result" => $eachMeeting);

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
	public function show($tournamentId) {
		//TODO: work with private tournaments
		//TODO: work with jackpot tournaments

		//does tournament exist?
		$tournamentModel = new \TopBetta\Tournament;
		$tournament = $tournamentModel -> find($tournamentId);

		if (is_null($tournament)) {

			return array('success' => false, 'error' => \Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId)));

		}

		//is this a racing or sports tournament
		$isRacingTournament = $tournamentModel -> isRacing($tournamentId);

		//looks like racing uses meeting_id & sports uses event_group_id ???
		//as the event_group_id
		$meetingId = $tournament -> event_group_id;

		if (!$isRacingTournament) {

			//get the comp_id and events list
			$eventGroup = \TopBetta\SportsComps::find($meetingId);

			$eventList = \TopBetta\SportsEvents::where('event_group_id', '=', $meetingId) -> get();

			$events = array();

			foreach ($eventList as $event) {

				$events[] = (int)$event -> event_id;

			}

		}

		//get entries/player list
		$ticketModel = new \TopBetta\TournamentTicket;
		$playerList = $ticketModel -> getTournamentEntrantList($tournamentId);

		//leaderboard
		$leaderboardModel = new \TopBetta\TournamentLeaderboard;

		$leaderboard = array();
		if (strtotime($tournament -> start_date) < time()) {

			if ($tournament -> paid_flag) {

				$leaderboard = $leaderboardModel -> getLeaderBoardRank($tournament, 50, true);

			} else {

				$leaderboard = $leaderboardModel -> getLeaderBoardRank($tournament, 50);

			}

			foreach ($leaderboard as $id => $val) {
				$leaderboard[$id] -> id = (int)$leaderboard[$id] -> id;
				$leaderboard[$id] -> currency = (int)$leaderboard[$id] -> currency;
				$leaderboard[$id] -> qualified = ($leaderboard[$id] -> qualified == 0) ? false : true;

				// we don't really need these showing
				unset($leaderboard[$id] -> name);
				unset($leaderboard[$id] -> turned_over);
			}

		}

		//get prize pool in cents & places paid
		$prizePool = $tournamentModel -> calculateTournamentPrizePool($tournamentId);
		$placeList = $tournamentModel -> calculateTournamentPlacesPaid($tournament, count($playerList), $prizePool);

		//work out places paid via place list
		$places_paid = 0;
		if ($placeList) {
			foreach ($placeList['place'] as $place => $prize) {
				$place_display[$place] = array();
				if (isset($prize['ticket']) && !empty($prize['ticket'])) {
					$place_display[$place][] = '1 Ticket (#' . $prize['ticket'] . ')';
				}

				if (isset($prize['cash']) && !empty($prize['cash'])) {
					$place_display[$place][] = $prize['cash'];
				}

				$place_display[$place] = join(' + ', $place_display[$place]);
			}
			$places_paid = count($place_display);
		}

		$numRegistrations = count($playerList);

		//calculate tournament end date/betting open

		// special case to send data back in a format for backbone - this ones for you Jase ;-)
		if (\Input::get('grouped') == true) {

			$tournamentDetails = array(
				'id' => (int)$tournament -> id,
				'buy_in' => (int)$tournament -> buy_in,
				'entry_fee' => (int)$tournament -> entry_fee,
				'num_entries' => (int)$numRegistrations,
				'prize_pool' => $prizePool,
				'places_paid' => $places_paid,
				'start_currency' => (int)$tournament -> start_currency,
				'start_date' => \TimeHelper::isoDate($tournament -> start_date),
				'end_date' => \TimeHelper::isoDate($tournament -> end_date)
			);

			$tournamentParent = \TopBetta\RaceMeeting::find($meetingId);
			return array('success' => true, 'result' => array(
				'id' => (int)$tournamentParent -> id,
				'name' => $tournamentParent -> name,
				'state' => $tournamentParent -> state,
				'weather' => $tournamentParent -> weather,
				'track' => $tournamentParent -> track,
				'sub_type' => strtolower($tournamentParent -> type_code),
				'tournament' => $tournamentDetails
			));

		}

		//our normal tournament info
		if ($isRacingTournament) {

			// ::: racing related data :::
			return array('success' => true, 'result' => array('parent_tournament_id' => (int)$tournament -> parent_tournament_id, 'meeting_id' => (int)$meetingId, 'name' => $tournament -> name, 'description' => $tournament -> description, 'start_currency' => (int)$tournament -> start_currency, 'start_date' => \TimeHelper::isoDate($tournament -> start_date), 'end_date' => \TimeHelper::isoDate($tournament -> end_date), 'end_date' => \TimeHelper::isoDate($tournament -> end_date), 'jackpot_flag' => ($tournament -> jackpot_flag == 0) ? false : true, 'num_registrations' => (int)$numRegistrations, 'buy_in' => (int)$tournament -> buy_in, 'entry_fee' => (int)$tournament -> entry_fee, 'paid_flag' => ($tournament -> paid_flag == 0) ? false : true, 'cancelled_flag' => ($tournament -> cancelled_flag == 0) ? false : true, 'cancelled_reason' => $tournament -> cancelled_reason, 'place_list' => $placeList, 'prize_pool' => $prizePool, 'players' => $playerList, 'leaderboard' => $leaderboard, 'places_paid' => $places_paid, 'private' => ($tournament -> private_flag == 0) ? false : true, 'password_protected' => false));

		} else {

			// ::: sports related data :::
			return array('success' => true, 'result' => array('parent_tournament_id' => (int)$tournament -> parent_tournament_id, 'competition_id' => (int)$meetingId, 'events' => $events, 'name' => $tournament -> name, 'description' => $tournament -> description, 'start_currency' => (int)$tournament -> start_currency, 'start_date' => \TimeHelper::isoDate($tournament -> start_date), 'end_date' => \TimeHelper::isoDate($tournament -> end_date), 'jackpot_flag' => ($tournament -> jackpot_flag == 0) ? false : true, 'num_registrations' => (int)$numRegistrations, 'buy_in' => (int)$tournament -> buy_in, 'entry_fee' => (int)$tournament -> entry_fee, 'closed_betting_on_first_match_flag' => ($tournament -> closed_betting_on_first_match_flag == 0) ? false : true, 'reinvest_winnings_flag' => ($tournament -> reinvest_winnings_flag == 0) ? false : true, 'paid_flag' => ($tournament -> paid_flag == 0) ? false : true, 'cancelled_flag' => ($tournament -> cancelled_flag == 0) ? false : true, 'cancelled_reason' => $tournament -> cancelled_reason, 'place_list' => $placeList, 'prize_pool' => $prizePool, 'players' => $playerList, 'leaderboard' => $leaderboard, 'places_paid' => $places_paid, 'private' => ($tournament -> private_flag == 0) ? false : true, 'password_protected' => false));

		}

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
