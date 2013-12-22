<?php
namespace TopBetta\frontend;

use TopBetta;

class FrontUsersTournamentsController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		$report = \Input::get('report', 'transactions');

		$type = \Input::get('type', null);

		$limit = \Input::get('per_page', 25);
		$page = \Input::get('page', 1);

		$offset = $limit * ($page - 1);

		$excludeSports = array('galloping', 'greyhounds', 'harness');

		if ($report == 'transactions') {

			//this runs a very heavy query - cache for 1 minute
			return \Cache::remember('usersTournamentTransactions-' . \Auth::user() -> id . '-' . $type . $limit . $page, 1, function() use (&$type, &$limit, &$offset, &$excludeSports, $page) {				

				$transactionModel = new \TopBetta\FreeCreditBalance;

				$transactionList = $transactionModel -> listTransactions(\Auth::user() -> id, $type, $limit, $offset);

				$transactions = array();

				foreach ($transactionList['result'] as $transaction) {

					//handle our description field
					$ticket = null;
					$tournament = null;
					if ($transaction -> buy_in_transaction_id) {

						$ticket = $transaction -> tournament_id;
						$tournament = $transaction -> tournament;
						$sport = in_array($transaction -> sport_name, $excludeSports) ? 'racing' : 'sports';

					}

					if ($transaction -> entry_fee_transaction_id) {

						$ticket = $transaction -> tournament_id2;
						$tournament = $transaction -> tournament2;
						$sport = in_array($transaction -> sport_name2, $excludeSports) ? 'racing' : 'sports';

					}

					if ($transaction -> result_transaction_id) {

						$ticket = $transaction -> tournament_id3;
						$tournament = $transaction -> tournament3;
						$sport = in_array($transaction -> sport_name3, $excludeSports) ? 'racing' : 'sports';

					}

					if ($ticket) {

						$description = htmlspecialchars($ticket) . htmlspecialchars($tournament ? $tournament : $transaction -> description);

					} else if ($transaction -> friend_username) {

						$description = "Referral payment for user " . htmlspecialchars($transaction -> friend_username);

					} else {

						$description = htmlspecialchars($transaction -> description);

					}

					if ($transaction -> bet_entry_id || $transaction -> bet_win_id) {

						$bet_id = ($transaction -> bet_entry_id ? $transaction -> bet_entry_id : $transaction -> bet_win_id);
						$description .= ' (Ticket: ' . $bet_id . ')';

					}

					// handle our type field
					if ($transaction -> amount > 0) {

						$transactionType = 'Deposit - ' . $transaction -> type;

					}

					if ($transaction -> amount < 0) {

						$transactionType = 'Withdrawal - ' . $transaction -> type;

					}

					//put it all together
					$transactions[] = array('id' => $transaction -> id, 'date' => \TimeHelper::isoDate($transaction -> created_date), 'description' => $description, 'value' => $transaction -> amount, 'type' => $transactionType);

				}

				$numPages = ceil($transactionList['num_rows'] -> total / $limit);
				return array("success" => true, "result" => array('transactions' => $transactions, 'num_pages' => (int)$numPages, 'current_page' => (int)$page));

			});

		} elseif ($report == 'history') {
			
			$userId = \Auth::user() -> id;

			//cache for 30 seconds (.5 min)
			return \Cache::remember('usersTournamentHistory-' . $userId . '-' . $type . $limit . $page, .5, function() use (&$userId, &$type, &$limit, &$offset, &$excludeSports, $page) {
				
				$ticket_model = new \TopBetta\TournamentTicket;	
					
				$tournament_list = $ticket_model->getUserTournamentList($userId, 'tk.id', 'DESC', $limit, $offset);
				$tournamentHistory = array();
				
				foreach ($tournament_list['result'] as $tournament) {
					//set bet open
					$tournament->bet_open		= strtotime($tournament->end_date) > time();
					//populate bettabucks
					$tournament->betta_bucks	= $ticket_model->getAvailableTicketCurrency($tournament->id, $userId);
					//get leaderboard rank
		
					$leaderboard_model				= new \TopBetta\TournamentLeaderboard;
					$leaderboard					= $leaderboard_model->getLeaderBoardRankByUserAndTournament($userId, $tournament);
					$tournament->leaderboard_rank	= $leaderboard->rank;
		
					$tournament->prize				= null;
					$tournament->ticket_awarded		= null;
		
					$tournament->type				= (in_array($tournament->sport_name, $excludeSports) ? 'racing' : 'sports');
		
					$transaction_record				= null;
					$parent_tournament				= null;
					if ($tournament->result_transaction_id) {
						if ($tournament->jackpot_flag && !empty($tournament->parent_tournament_id) && -1 != $tournament->parent_tournament_id) {
							$transaction_record = \TopBetta\FreeCreditBalance::find($tournament->result_transaction_id);
							$parent_tournament = \TopBetta\Tournament::find($tournament->parent_tournament_id);
						} else {
							$transaction_record = \TopBetta\AccountBalance::find($tournament->result_transaction_id);
						}
					}
					if ($transaction_record && $transaction_record->amount > 0) {
						$tournament->prize = $transaction_record->amount;
		
						if ($tournament->jackpot_flag && !empty($parent_tournament) && -1 != $tournament->parent_tournament_id) {
							$ticket_cost = $parent_tournament->entry_fee + $parent_tournament->buy_in;
		
							if ($tournament->prize > $ticket_cost) {
								$tournament->ticket_awarded	= $parent_tournament->id;
								$tournament->prize			= $tournament->prize - $ticket_cost;
							}
						}
					}
					
					//buid our single tournament history row						
					$prize = (empty($tournament->ticket_awarded) && empty($tournament->prize)) ? '-' : null;
					if ($tournament->ticket_awarded) {
						$prize .= '1 Ticket (#' . $tournament->ticket_awarded .')';
					}
					
					//TODO: is this really needed?
					if ($tournament->prize) {
						//$prize .= ' + ';	
					}					
					
					$tournamentHistory[] = array(
						'id' => (int)$tournament->id,
						'sport' => $tournament->sport_name . ' - ' . $tournament->tournament_name,
						'tournament_name' => $tournament->tournament_name,
						'start_date' => \TimeHelper::isoDate($tournament->start_date),
                        'end_date' => \TimeHelper::isoDate($tournament->end_date),
						'total' => (int)$tournament->betta_bucks,
						'place' => $tournament->leaderboard_rank,
						'prize' => $prize,
						'prize_amount' => (int)$tournament->prize
					);
				}

				$numPages = ceil($tournament_list['num_rows'] -> total / $limit);
				return array("success" => true, "result" => array('transactions' => $tournamentHistory, 'num_pages' => (int)$numPages, 'current_page' => (int)$page));				
				
			});

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
