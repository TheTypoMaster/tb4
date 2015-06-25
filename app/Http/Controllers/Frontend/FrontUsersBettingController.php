<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;
use TopBetta;

class FrontUsersBettingController extends Controller {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$limit = \Input::get('per_page', 25);
		$page = \Input::get('page', 1);

		$offset = $limit * ($page - 1);

		$filter = array('result_type' => \Input::get('type', false),
		'limitstart' => $offset
		//'from_time'		=> $filter_from_date ? strtotime($filter_from_date) : null,
		//'to_time'		=> $filter_to_date ? (strtotime($filter_to_date) + 24 * 60 * 60) : null,
		);

		return \Cache::remember('usersBettingHistory-' . \Auth::user() -> id . '-' . $filter['result_type'] . $limit . $page, 1, function() use (&$type, &$limit, &$offset, $filter, $page) {

			//pass data onto legacy api
			$l = new \TopBetta\Helpers\LegacyApiHelper;
			$history = $l -> query('getBettingHistory', $filter);

			if ($history['status'] == 200) {

				$transactions = array();

				foreach ($history['bet_list'] as $key => $transaction) {

					$transactions[] = array('id' => $key, 'date' => \TopBetta\Helpers\TimeHelper::isoDate($transaction['bet_time']), 'selections' => $transaction['label'], 'bet_type' => $transaction['bet_type'], 'bet_amount' => (int)$transaction['amount'], 'bet_total' => (int)$transaction['bet_total'], 'freebet_amount' => (int)$transaction['bet_freebet_amount'], 'dividend' => (float)$transaction['dividend'], 'paid' => (int)$transaction['paid'], 'result' => $transaction['result'], 'half_refund' => $transaction['half_refund']);

				}

				return array("success" => true, "result" => array('transactions' => $transactions, 'num_pages' => (int)$history['pagination']['pages.stop'], 'current_page' => (int)$history['pagination']['pages.current']));

			} else {

				return array("success" => false, "error" => $history['error_msg']);

			}

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
