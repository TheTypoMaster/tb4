<?php
require_once '../common/shell-bootstrap.php';

class FillLeaderboard extends TopBettaCLI
{
	private $leaderboard;

	private $ticket;

	private $tournament;

	private $db;

	const ENTRANT_COUNT_DEFAULT = 25;

	public function __construct() {
		parent::__construct();

		$this->addComponentModels('tournament');
		$this->addComponentModels('topbetta_user');

		$this->leaderboard 	=& JModel::getInstance('TournamentLeaderboard', 'TournamentModel');
		$this->ticket 		=& JModel::getInstance('TournamentTicket', 'TournamentModel');
		$this->tournament	=& JModel::getInstance('TournamentRacing', 'TournamentModel');
		$this->user			=& JModel::getInstance('TopbettaUser', 'TopbettaUserModel');

		$this->db = $this->tournament->_db;
	}

	public function execute() {
		$id = $this->arg('id');
		if(empty($id)) {
			$this->l('No tournament ID provided', self::LOG_TYPE_ERROR);
			return;
		}

		$tournament = $this->tournament->getTournamentRacingByTournamentID($id);
		$entrant_count = $this->arg('entrant-count');

		if(empty($entrant_count)) {
			$entrant_count = self::ENTRANT_COUNT_DEFAULT;
		}

		$user_list = array();
		for($i = 0; $i < $entrant_count; ++$i) {
			do {
				$user_id = $this->_getRandomUserID();
			} while(in_array($user_id, $user_list));

			$user_list[] = $user_id;

			$user = $this->user->getUser($user_id);
			$this->l("Creating ticket for {$user->username}");

			$ticket = array(
				'tournament_id' => $tournament->id,
				'user_id' 		=> $user->id
			);

			$this->ticket->store($ticket);
			$currency = mt_rand(0, 3000000);

			$this->l("Setting leaderboard currency for {$user->username} to {$currency}");
			$leaderboard = array(
				'tournament_id' => $tournament->id,
				'user_id' 		=> $user_id,
				'currency' 		=> $currency,
				'turned_over' 	=> $tournament->start_currency
			);

			$this->leaderboard->store($leaderboard);
		}
	}

	private function _getRandomUserID() {
		$query =
			'SELECT
				id
			FROM
				' . $this->db->nameQuote('#__users') . '
			ORDER BY
				RAND()
			LIMIT 1';

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
}

$script = new FillLeaderboard();
$script->debug(true);
$script->execute();