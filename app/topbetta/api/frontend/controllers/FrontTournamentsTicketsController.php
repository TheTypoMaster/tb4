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

		$errorMessages = array();

		foreach ($tournaments['tournaments'] as $tournamentId) {

			// save tournament tickets via legacy API
			$l = new \TopBetta\LegacyApiHelper;
			$ticket = $l -> query('saveTournamentTicket', array("id" => $tournamentId, 'username' => \Auth::user() -> username));

			if ($ticket['status'] != 200) {

				$errorMessages[] = array("id" => $tournamentId, "error" => $ticket['error_msg']);

			}

		}

		//TODO: how should we handle partial success - some tickets ok
		if (count($errorMessages) > 0) {

			return array("success" => false, "error" => $errorMessages);

		} else {

			return array("success" => true, "result" => "Tournament tickets purchased.");

		}

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
