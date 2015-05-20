<?php namespace TopBetta\Http\Frontend\Controllers;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontUsersBankingController extends \BaseController {

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

		//types: deposits_withdrawals,bets,tournaments
		$type = \Input::get('type', null);

		$limit = \Input::get('per_page', 25);
		$page = \Input::get('page', 1);

		$offset = $limit * ($page - 1);

		$excludeSports = array('galloping', 'greyhounds', 'harness');

		if ($report == 'transactions') {

			//this runs a very heavy query - cache for 1 minute
			return \Cache::remember('usersBankingTransactions-' . \Auth::user() -> id . '-' . $type . $limit . $page, 1, function() use (&$type, &$limit, &$offset, &$excludeSports, $page) {

				$accountModel = new \TopBetta\Models\AccountBalance;
				$transactionList = $accountModel -> listTransactions(\Auth::user() -> id, $type, $limit, $offset);

				$transactions = array();

				foreach ($transactionList['result'] as $transaction) {

					$sport = in_array($transaction -> sport_name, $excludeSports) ? 'racing' : 'sports';

					if ($transaction -> tournament && !$transaction -> ticket_refunded_flag) {
						$description = $transaction -> tournament;
					} else {
						$description = $transaction -> description;
					}
					
					if ($transaction -> bet_entry_id || $transaction -> bet_win_id) {

						$bet_id = ($transaction -> bet_entry_id ? $transaction -> bet_entry_id : $transaction -> bet_win_id);
						$description .= ' (Ticket: ' . $bet_id . ')';

					}					
					
					// handle our type field
					if ($transaction -> amount > 0) {

						$transactionType = 'Deposit - ' . $transaction -> type;

					}

					else if ($transaction -> amount < 0) {

						$transactionType = 'Withdrawal - ' . $transaction -> type;

					}					
					
					$transactions[] = array('id' => $transaction -> id, 'date' => \TimeHelper::isoDate($transaction -> created_date), 'description' => $description, 'value' => $transaction -> amount, 'type' => $transactionType);

				}
 
				$numPages = ceil($transactionList['num_rows'] -> total / $limit);
				return array("success" => true, "result" => array('transactions' => $transactions, 'num_pages' => (int)$numPages, 'current_page' => (int)$page));

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
