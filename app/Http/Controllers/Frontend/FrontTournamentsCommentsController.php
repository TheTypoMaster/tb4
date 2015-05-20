<?php namespace TopBetta\Http\Frontend\Controllers;

// use TopBetta;

class FrontTournamentsCommentsController extends \BaseController {

	public function __construct() {

		//we are only protecting certain routes in this controller
		$this -> beforeFilter('auth', array('only' => array('store')));

	}

	/**
	 * Display a listing of the resource.
	 * @param  [type] $id Tournament ID
	 * @return Response
	 */
	public function index($tournamentId)
	{
		//does tournament exist?
		$tournamentModel = new \TopBetta\Tournament;
		$tournament = $tournamentModel -> find($tournamentId);

		if ($tournament) {
			$limit = \Input::get('limit', 50);
			$dir = \Input::get('dir', false);
			$commentId = \Input::get('comment_id', false);

			$tournamentComments = \TopBetta\TournamentComment::getTournamentCommentListByTournamentId($tournamentId, $limit, $dir, $commentId);

			$comments = array();

			foreach ($tournamentComments as $comment) {
				$comments[] = array(
					'id' => (int)$comment->id,
					'tournament_id' => (int)$comment->tournament_id,
					'username' => $comment->username,
					'comment' => $comment->comment,
					'date' => \TimeHelper::isoDate($comment->created_at)
					);
			}

			$commentsAllowed = \TopBetta\TournamentComment::isTournamentCommentingAllowed($tournamentId);

			return array("success" => true, "result" => array(
					"comments_allowed" => $commentsAllowed,
					"comments" => $comments)
				);

		} else {

			return array('success' => false, 'error' => \Lang::get('tournaments.not_found', array('tournamentId' => $tournamentId)));

		}		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($tournamentId)
	{	
		$input = \Input::json() -> all();

		$rules = array('comment' => 'required|max:400');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {					

			$ticket = \TopBetta\Models\TournamentTicket::getTicketForUserId(\Auth::user()->id, $tournamentId);

			if (count($ticket) == 0) {
				return array("success" => false, "error" => \Lang::get('tournaments.ticket_not_found'));
			}

			// only let them post comments for 2 days after the tournament end date
			if (!\TopBetta\TournamentComment::isTournamentCommentingAllowed($tournamentId)) {
				return array("success" => false, "error" => \Lang::get('tournaments.commenting_closed'));
			}

			$comment = $input['comment'];

			/**
			 * Replace bad words with asterisks
			 */
			// TODO: this list should be maintained in the database really
			$blacklist = array('cock','cunt','dyke','faggot','fag','fuck','nigger','prick','asshole');

			foreach ($blacklist as $badword){
				/**
				 * Replacing all characters of the bad word with *
				 */
				$comment = str_ireplace($badword, str_repeat('*', strlen($badword)), $comment);
			}

			$params	= array(
				"tournament_id" => $tournamentId,
				"user_id" => \Auth::user()->id,
				"comment" => $comment
			);

			$tournamentComment = \TopBetta\TournamentComment::create($params);

			if ($tournamentComment) {
				return array("success" => true, "result" => \Lang::get('tournaments.comment_posted'));
			} else {
				return array("success" => false, "error" => \Lang::get('tournaments.comment_issue'));	
			}

		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}