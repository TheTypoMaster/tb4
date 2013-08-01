<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

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

			return array('success' => false, 'error' => Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId)));
			
		}

		//is this a racing or sports tournament
		$isRacingTournament = $tournamentModel -> isRacing($tournamentId);

		//looks like racing uses meeting_id & sports uses event_group_id ???
		//as the event_group_id
		$meetingId = $tournament -> event_group_id;

		//get entries/player list
		$ticketModel = new \TopBetta\TournamentTicket;		
		$playerList = $ticketModel -> getTournamentEntrantList($tournamentId);

		//leaderboard
		$leaderboardModel = new \TopBetta\TournamentLeaderboard;
		
		$leaderboard = array();
		if (strtotime($tournament->start_date) < time()) {
				
			if ($tournament->paid_flag) {
			
				$leaderboard = $leaderboardModel->getLeaderBoardRank($tournament, 50, true);
				
			} else {
					
				$leaderboard = $leaderboardModel->getLeaderBoardRank($tournament, 50);
				
			}
			
			foreach($leaderboard as $id =>$val) {
				$leaderboard[$id]->id = (int)$leaderboard[$id]->id;	
				$leaderboard[$id]->currency = (int)$leaderboard[$id]->currency;
				$leaderboard[$id]->qualified = ($leaderboard[$id]->qualified == 0) ? false : true;
				
				// we don't really need these showing
				unset($leaderboard[$id]->name);
				unset($leaderboard[$id]->turned_over);
			}	
			
		}		

		//get prize pool in cents & places paid
		$prizePool = $tournamentModel -> calculateTournamentPrizePool($tournamentId) * 100;
		$placeList = $tournamentModel -> calculateTournamentPlacesPaid($tournament, count($playerList), $prizePool);

		//work out places paid via place list
		 $places_paid = 0;
		 if ($placeList) {
			foreach($placeList['place'] as $place => $prize) {
				$place_display[$place] = array();
				if(isset($prize['ticket']) && !empty($prize['ticket'])) {
					$place_display[$place][] = '1 Ticket (#' . $prize['ticket'] . ')';
				}

				if(isset($prize['cash']) && !empty($prize['cash'])) {
					$place_display[$place][] = $prize['cash'];
				}

				$place_display[$place] = join(' + ', $place_display[$place]);
			}
			$places_paid    = count($place_display);						
		 }		

        $numRegistrations = count($playerList);	

		//calculate tournament end date/betting open
	
				
		if ($isRacingTournament) {
			// ::: racing related data :::

			//our data to send back
			return array('success' => true, 'result' => array('parent_tournament_id' => (int)$tournament -> parent_tournament_id, 'meeting_id' => (int)$meetingId, 'name' => $tournament -> name, 
			'description' => $tournament -> description, 'start_currency' => (int)$tournament -> start_currency, 'start_date' => \TimeHelper::isoDate($tournament -> start_date),
			'end_date' => \TimeHelper::isoDate($tournament -> end_date), 'jackpot_flag' => ($tournament -> jackpot_flag == 0) ? false : true, 'num_registrations' => (int)$numRegistrations, 'buy_in' => (int)$tournament -> buy_in, 'entry_fee' => (int)$tournament -> entry_fee, 
			'paid_flag' => ($tournament -> paid_flag == 0) ? false : true, 'cancelled_flag' => ($tournament -> cancelled_flag == 0) ? false : true, 'cancelled_reason' => $tournament -> cancelled_reason, 
			'place_list' => $placeList, 'prize_pool' => $prizePool, 'players' => $playerList, 'leaderboard' => $leaderboard, 'places_paid' => $places_paid, 'private' => ($tournament -> private_flag == 0) ? false : true, 
			'password_protected' => false));
			
		} else {
			// ::: sports related data :::
			return array('success' => true, 'result' => array());

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
