<?php
require_once '../common/shell-bootstrap.php';


class PopulateFinalPayoutTable extends TopBettaCLI
{
	public function initialise() {
		$this->addComponentModels('tournament');
		$this->payout_final_model =& JModel::getInstance('TournamentPayoutFinal', 'TournamentModel');
		$this->tournament =& JModel::getInstance('Tournament', 'TournamentModel');
		$this->places_paid =& JModel::getInstance('TournamentPlacesPaid', 'TournamentModel');
		$this->private =& JModel::getInstance('TournamentPrivate', 'TournamentModel');
		$this->leaderboard =& JModel::getInstance('TournamentLeaderBoard', 'TournamentModel');

		$this->db =& $this->tournament->_db;

		ini_set('memory_limit','128M');
	}

	public function execute() {
		while($tournament = $this->_getNextTournament()){
			$this->l('Saving result payout for: '.$tournament->name);

			if($this->_isPrivate($tournament)){
				$this->_appendPrivateTournamentPrizeFormat($tournament);

				$tournament_private = $this->private->getTournamentPrivateByTournamentID($tournament->id);
			}

			$qualified_list = $this->leaderboard->getLeaderBoardRank($tournament, null, true);
			$qualified = count($qualified_list);
			$this->l("Found {$qualified} qualifier(s) for tournament {$tournament->name} ({$tournament->id})");

			if($qualified == 0) {
				continue;
			}

			$prize_list 		= $this->places_paid->getPrizeDistribution($tournament);
			$formula			= $prize_list['formula'];
			$prize_place_list 	= $prize_list['place'];
			$rank_list			= $this->places_paid->formatRankingList($qualified_list);

			$this->l("Using payout formula {$formula}");
			foreach($prize_place_list as $rank => $prize) {
				$this->l("Paying position {$rank}");
				foreach($rank_list[$rank] as $qualifier) {
					$this->d($qualifier);
					$this->d($prize);
					$ticket = (!empty($prize['ticket'])) ? $prize['ticket'] : null;
					$cash	= (!empty($prize['cash'])) ? $prize['cash'] : null;

					$payout_final =& $this->payout_final_model;

					$payout_final->tournament_id = (int)$tournament->id;
					$payout_final->user_id = (int)$qualifier->id;
					$payout_final->position = $rank;
					$payout_final->win_amount = (int)$cash;

					if($formula == 'cash') {
						$payout_final->saveCashPayout();
					} else {
						if(!is_null($ticket)){
							$payout_final->win_amount = $this->_getParentTicketValue($ticket);
							$payout_final->saveTournamentTicketPayout();
						}
						if(!is_null($cash)){
							$payout_final->win_amount = (int)$cash;
							$payout_final->saveTournamentDollarPayout();
						}
					}
				}

			}
		}
	}

	private function _getNextTournament(){
		static $tournament_list = null;

		if(is_null($tournament_list)){
			$tournament_list = $this->_getTournamentIdList();
		}

		$current_tournament_id = each($tournament_list);

		return $this->tournament->getTournament($current_tournament_id['value']);
	}

	private function _getTournamentIdList(){
		$query =
			'SELECT
				id
			FROM
				tbdb_tournament
			WHERE
				cancelled_flag <>1
			AND
				paid_flag =1';

		$this->db->setQuery($query);
		return $this->db->loadResultArray();
	}

	private function _isPrivate($tournament){
		return ($tournament->private_flag);
	}

	private function _appendPrivateTournamentPrizeFormat(&$tournament){
		$tournament_private = $this->private->getTournamentPrivateByTournamentID($tournament->id);
		$tournament->prize_format_id = $tournament_private->tournament_prize_format_id;
	}

	private function _getParentTicketValue($id){
		$parent_tournament = $this->tournament->getTournament($id);
		return $parent_tournament->buy_in + $parent_tournament->entry_fee;
	}
}

$utility = new PopulateFinalPayoutTable();
$utility->debug(false);
$utility->execute();