<?php
namespace TopBetta\frontend;

use TopBetta;
use Illuminate\Support\Facades\Input;

class FrontTournamentsTicketsController extends \BaseController {

	public function __construct() {
		$this -> beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//
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

		$tournaments = Input::json() -> all();

		$messages = array();
		$errors = 0;

		foreach ($tournaments['tournaments'] as $tournamentId) {

			// save tournament tickets via legacy API
			$l = new \TopBetta\LegacyApiHelper;
			$ticket = $l -> query('saveTournamentTicket', array("id" => $tournamentId));

			if ($ticket['status'] == 200) {

				$messages[] = array("id" => $tournamentId, "success" => true, "result" => $ticket['success']);

			} else {

				$messages[] = array("id" => $tournamentId, "success" => false, "error" => $ticket['error_msg']);
				$errors++;

			}

		}

		return array("success" => ($errors > 0) ? false : true, ($errors > 0) ? "error" : "result" => $messages);

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
