<?php
namespace TopBetta\frontend;

use TopBetta;

class FrontTournamentsCommentsController extends \BaseController {

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
			$tournamentComments = \TournamentComment::getTournamentCommentListByTournamentId($tournamentId);

			$comments = array();

			foreach ($tournamentComments as $comment) {
				$comments[] = array(
					'id' => (int)$comment->id,
					'tournament_id' => (int)$comment->tournament_id,
					'username' => $comment->username,
					'comment' => $comment->comment,
					'date' => \TimeHelper::isoDate($comment->created_date)
					);
			}

			return array("success" => true, "result" => $comments);

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
	public function store()
	{
		// Only registered users in this tournament can post comments

		//TODO: joomla code!		
		if (is_null($ticket) && ($user->usertype == "Registered" || $user->guest)) {
			$allow_sledge_comment = false;
		}		






		$comment 			= strip_tags(JRequest::getVar('tournament_sledge', '')); //cleaning up the html
		$tournament_id		= JRequest::getVar('tournament_id', null);
		$display_identifier	= JRequest::getVar('display_identifier', null);
		$ticket_model 		=& $this->getModel('TournamentTicket', 'TournamentModel');

		if (!empty($display_identifier)) {
			$redirect_url = JURI::base()."private/".$display_identifier;
		} else {
			$redirect_url = JURI::base() . "tournament/details/" . $tournament_id;
		}

		$this->setRedirect($redirect_url);

		$user =& JFactory::getUser();
		if ($user->guest) {
			JError::raiseWarning(0, JText::_('Please login to post a comment'));
			return false;
		}
		/**
		 * If the user is Normal user,
		 * he needs to be resigstered to the tournament
		 * To post a comment
		 */
		$ticket	= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament_id);

		/**
		 * If the user is Normal user,
		 * he needs to be resigstered to that tournament
		 * To post a comment
		 */
		if (is_null($ticket) && ($user->usertype == "Registered" || $user->guest)) {
			JError::raiseWarning(0, JText::_('You need to be in the tournament to post a comment'));
			return false;
		}
		if (strlen($comment) > 400) {
			JError::raiseWarning(0, JText::_('You are allowed maximum 400 characters per post'));
			return false;
		}
		if(empty($comment)){
			JError::raiseWarning(0, JText::_("Comment can't be Empty"));
			return false;
		}
		/**
		 * Replace bad words with asterisks
		 */
		$config 		=& JComponentHelper::getParams( 'com_topbetta_user' );
		$blacklist 		= $config->get('blacklistWords');
		$blacklist		= explode("\n", $blacklist); //Array of blacklisted words
		foreach ($blacklist as $key => $badword){
			/**
			 * Replacing all characters of the bad word with *
			 */
			$comment = str_replace($badword, str_repeat('*', strlen($badword)), $comment);
		}

		$tournament_comment_model 	=& $this->getModel('TournamentComment', 'TournamentModel');

		$params	= array(
			"tournament_id" => $tournament_id,
			"user_id" => $user->id,
			"comment" => $comment
		);

		$result	= $tournament_comment_model->store($params);

		$this->setMessage(JText::_("Comment Posted!"));
		return true;

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($tournamentId, $commentId)
	{
		//
		return "Showing Comment id: " . $commentId . " for Tournament ID: $tournamentId";
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