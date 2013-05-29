<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsDetailsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($tournamentId) {

		//TODO: work with private tournaments
		//TODO: work with jackpot tournaments

		//does tournament exist?
		$tournamentModel = new \TopBetta\Tournament;
		$tournament = $tournamentModel -> find($tournamentId);

		if (is_null($tournament)) {

			return array('success' => false, 'error' => "Tournament id: $tournamentId not found");
			
		}

		//is this a racing or sports tournament
		$isRacingTournament = $tournamentModel -> isRacing($tournamentId);

		//NOTE: looks like racing uses meeting_id & sports uses event_group_id ???
		//as the event_group_id (line 156 racing.php)
		$meetingId = $tournament -> event_group_id;

		//get the users tournament ticket (line 198)
		//TODO: pass in the current logged in user id
		$ticketModel = new \TopBetta\TournamentTicket;
		//$ticket = $ticketModel -> getTournamentTicketByUserAndTournamentID(1, $tournamentId);

		//get entries/player list
		$playerList = $ticketModel -> getTournamentEntrantList($tournamentId);

		//TODO: get leaderboard

		//get prize pool & places paid
		$prizePool = $tournamentModel -> calculateTournamentPrizePool($tournamentId);

		//TODO: places paid requires some more models yet
		//$placeList = $tournamentModel -> calculateTournamentPlacesPaid($tournament, count($playerList), $prizePool);

		//work out places paid via place list (line 338)

		//get tournament buy in, num entries, start time etc (line 355)

		//calculate tournament end date/betting open

		//calculate the users betta bucks if logged in (line 372)		

		//get all bets for this ticket and race number
				
		if ($isRacingTournament) {
			// ::: racing related data :::

			//next race
			//TODO: should this be done with the RaceEvents model instead?
			//find the event by the tourn id, then lookup race model for next race
			$tournamentRaceModel = new \TopBetta\TournamentRace;
			$nextRace = $tournamentRaceModel -> getNextRaceNumberByTournamentID($tournamentId);

			//race list
			$raceList = $tournamentRaceModel -> getRaceListByTournamentID($tournamentId);

			//are all races finished? Why do we need this?

			//did they pass in a specific race number?
			$raceNumber = Input::get('race_number', $nextRace);

			//get the runners for this race
			$raceId = $tournamentRaceModel -> getRaceIdForRaceNumber($tournamentId, $raceNumber);
			$runnersList = \TopBetta\RaceSelection::getRunnersForRaceId($raceId);
			
			//get race and results for race number

			//our data to send back
			return array('status' => true, 'result' => array('tournament' => null, 'meeting_id' => $meetingId, 'race_number' => (int)$raceNumber, 'next_race' => $nextRace, 'place_list' => null, 'prize_pool' => null, 'leaderboard_rank' => null, 'players' => $playerList, 'leaderboard' => null, 'runners' => $runnersList, 'tournament_bet_list' => null, 'places_paid' => null, 'races_and_results' => $raceList, 'available_currency' => null, 'private' => $tournament -> private_flag, 'password_protected' => null));
			
		} else {
			// ::: sports related data :::
			return array('status' => true, 'result' => array());

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
