<?php
namespace TopBetta\frontend;

use Auth;
use TopBetta;
use \Carbon\Carbon;

class FrontUsersTournamentsController extends \BaseController {

	/**
	 * @var \TopBetta\Repositories\UserTicketsRepository
	 */
	private $userTicketsRepository;

	public function __construct(TopBetta\Repositories\UserTicketsRepository $userTicketsRepository) {
//		$this -> beforeFilter('auth');
		$this->userTicketsRepository = $userTicketsRepository;
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
		$racingMap = array('galloping' => 'r', 'greyhounds' => 'g', 'harness' => 'h');

		if ($report == 'transactions') {

			//this runs a very heavy query - cache for 1 minute
			return \Cache::remember('usersTournamentTransactions-' . Auth::user() -> id . '-' . $type . $limit . $page, 1, function() use (&$type, &$limit, &$offset, &$excludeSports, $page) {

				$transactionModel = new \TopBetta\FreeCreditBalance;

				$transactionList = $transactionModel -> listTransactions(Auth::user() -> id, $type, $limit, $offset);

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
			
			$userId = Auth::user() -> id;

			//cache for 30 seconds (.5 min)
			return \Cache::remember('usersTournamentHistory-' . $userId . '-' . $type . $limit . $page, .5, function() use (&$userId, &$type, &$limit, &$offset, &$excludeSports, $page, $racingMap) {
				
				$ticket_model = new \TopBetta\TournamentTicket;	

                // just grab the completed tournaments - this API needs to be re-addressed at some stage.
                $paid = 1;

				$tournament_list = $ticket_model->getUserTournamentList($userId, 'tk.id', 'DESC', $limit, $offset, $paid);
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
					$tournament->num_entries		= $ticket_model->countTournamentEntrants($tournament->id);
		
					$tournament->prize				= null;
					$tournament->ticket_awarded		= null;
		
					$tournament->type				= (in_array($tournament->sport_name, $excludeSports) ? 'racing' : 'sports');
					$tournament->sub_type			= (in_array($tournament->sport_name, $excludeSports) ? $racingMap[$tournament->sport_name] : $tournament->sport_name);

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
						'sub_type' => $tournament->sub_type,
						'tournament_name' => $tournament->tournament_name,
						'start_date' => \TimeHelper::isoDate($tournament->start_date),
                        'end_date' => \TimeHelper::isoDate($tournament->end_date),
						'total' => (int)$tournament->betta_bucks,
						'place' => $tournament->leaderboard_rank,
						'num_entries' => (int)$tournament->num_entries,
						'prize' => $prize,
						'prize_amount' => (int)$tournament->prize,
                        'entry_fee' => $tournament->entry_fee,
                        'buy_in' => $tournament->buy_in,
                        'tournament_sponsor_name' => $tournament->tournament_sponsor_name,
                        'reinvest_winnings_flag' => $tournament->reinvest_winnings_flag,
                        'closed_betting_on_first_match_flag' => $tournament->closed_betting_on_first_match_flag
					);
				}

				$numPages = ceil($tournament_list['num_rows'] -> total / $limit);
				return array("success" => true, "result" => array('transactions' => $tournamentHistory, 'num_pages' => (int)$numPages, 'current_page' => (int)$page));				
				
			});

		}

	}

	public function usersTournamentHistory() {

		// Get the logged in user
		$user = Auth::user();

		// Set the user to the currently logged in user
		$this->userTicketsRepository->setUser($user);

		// Get all of the users tournament tickets and tournaments. If a 'since' date was not passed in, default to the
		// last two dats

		$c = new Carbon();
		$twoDaysAgo = $c->subDays(2);
		$sinceDate = \Input::get('since', $twoDaysAgo);
		$ticketsList = $this->userTicketsRepository->getUsersTicketsAndTournaments($sinceDate)->toArray();

		// Create a new instance of a tournaments repository. This repository will be re-used while iterating through the
		// ticket list in order to get that tournaments leaderboard.
		$tournamentsRepository = App::make('\TopBetta\TournamentsRepository');

		var_dump($ticketsList);

		foreach ($ticketsList as $ticket) {
			$tournamentsRepository->setModel($ticket['tournament']);
			var_dump($tournamentsRepository->getTournamentLeaderboard());
			echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
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
