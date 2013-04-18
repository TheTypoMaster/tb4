<?php
require_once '../common/shell-bootstrap.php';

class FixTournament extends TopBettaCLI
{
	private $leaderboard;

	private $ticket;

	private $tournament;

	private $db;

	private $transaction;

	private $user;

	const INCLUDE_REFUNDED = 1;

	public function initialise() {
		$this->addComponentModels('tournament');
		$this->addComponentModels('topbetta_user');
		$this->addComponentModels('tournamentdollars');

		$this->tournament   =& JModel::getInstance('Tournament', 'TournamentModel');
		$this->leaderboard 	=& JModel::getInstance('TournamentLeaderboard', 'TournamentModel');
		$this->ticket 		=& JModel::getInstance('TournamentTicket', 'TournamentModel');
		$this->transaction  =& JModel::getInstance('TournamentTransaction', 'TournamentDollarsModel');
		$this->user			=& JModel::getInstance('TopbettaUser', 'TopbettaUserModel');

		$this->db = $this->tournament->_db;
	}

	public function execute() {
		$id = $this->arg('id');
		if(empty($id)) {
			$this->l('No tournament ID provided', self::LOG_TYPE_ERROR);
			return;
		}

		$tournament = $this->tournament->getTournament($id);
		$tournament_ticket_list = $this->ticket->getTournamentTicketListByTournamentId($id, self::INCLUDE_REFUNDED);

		$this->l('Processing Tournament '. $tournament->name .' (' . $tournament->id . ')');

		$ticket_cost = $tournament->buy_in + $tournament->entry_fee;
		$ignore_email_list = array();
		$email_list = array();

		foreach($tournament_ticket_list as $key => $ticket){

			$user = $this->user->getUser($ticket->user_id);

			$this->l('Tournament ticket: ' . $ticket->id . ', User: ' . $user->username . ' ('. $ticket->user_id .')');
			if($ticket->refunded_flag == 1){
				$this->l('Ticket has been refunded');
				$refunded = $this->_getRefundAmount($ticket->result_transaction_id);

				if($refunded < $ticket_cost){
					$this->_refundEntryFee($ticket, $tournament->entry_fee);
					$this->_clearLeaderboardRecord($ticket);
					$email_list[] = $user->email;
				}
			}
			else{
				$ignore_email_list[] = $user->email;
			}
		}

		$this->_resetTournamentPaidFlag($id);

		$email = implode(',', array_diff($email_list, $ignore_email_list));
		$this->l('Users to email: ' . $email);
	}

	private function _getRefundAmount($id){
		return $this->transaction->getTournamentTransaction($id)->amount;
	}

	private function _refundEntryFee($ticket, $entry_fee) {

		$this->transaction->setUserId($ticket->user_id);
		$refund_id = $this->transaction->increment($entry_fee, 'refund');

		$this->db->setQuery($query);
		$this->db->query();
		$this->l('User has had $'. number_format($entry_fee / 100, 2) .' entry fee refunded.');
	}

	private function _clearLeaderBoardRecord($ticket){
		$query = '
			DELETE
			FROM
				' . $this->db->nameQuote('#__tournament_leaderboard') . '
			WHERE
				user_id = ' . $this->db->quote($ticket->user_id) . '
			AND
				tournament_id = ' . $this->db->quote($ticket->tournament_id) . '
			LIMIT 1
		';

		$this->db->setQuery($query);
		if($this->db->query()){
			$this->l('Single user leaderboard entry cleared.');
		}
	}

	private function _resetTournamentPaidFlag($id) {
		$query =
			'UPDATE
				' . $this->db->nameQuote( '#__tournament' ) . '
			SET
				paid_flag = 0,
				updated_date = NOW()
			WHERE
				id = ' . $this->db->quote($id);

		$this->db->setQuery($query);
		if($this->db->query()){
			$this->l('Tournament paid flag reset.');
		}
	}
}

$script = new FixTournament();
$script->debug(true);
$script->execute();