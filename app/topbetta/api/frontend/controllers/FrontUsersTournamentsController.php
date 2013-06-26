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

		$transactionType = \Input::get('type', null);

		$limit = \Input::get('per_page', 25);
		$page = \Input::get('page', 1);

		$offset = $limit * ($page - 1);

		//this runs a very heavy query - cache for 1 minute
		return \Cache::remember('usersTournamentTransactions-' . \Auth::user() -> id . '-' . $transactionType . $limit . $page, 1, function() use (&$transactionType, &$limit, &$offset) {

			$excludeSports = array('galloping', 'greyhounds', 'harness');

			$transactionModel = new \TopBetta\FreeCreditBalance;

			$transactionList = $transactionModel -> listTransactions(\Auth::user() -> id, $transactionType, $limit, $offset);

			$transactions = array();

			foreach ($transactionList as $transaction) {

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

					$type = 'Deposit - ' . $transaction -> type;

				}

				if ($transaction -> amount < 0) {

					$type = 'Withdrawal - ' . $transaction -> type;

				}

				//put it all together
				$transactions[] = array('id' => $transaction -> id, 'date' => \TimeHelper::isoDate($transaction -> created_date), 'description' => $description, 'value' => $transaction -> amount, 'type' => $type);

			}

			return $transactions;

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
