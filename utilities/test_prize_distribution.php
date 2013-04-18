<?php
require_once '../common/shell-bootstrap.php';

class TestPlace extends TopBettaCLI
{
	private $tournament;

	private $place;

	const ENTRANT_COUNT_DEFAULT = 100;

	public function __construct() {
		parent::__construct();

		$this->addComponentModels('tournament');

		$this->tournament 	= JModel::getInstance('TournamentRacing', 'TournamentModel');
		$this->place 		= JModel::getInstance('TournamentPlacesPaid', 'TournamentModel');
	}

	public function execute() {
		$id = $this->arg('id');
		if(empty($id)) {
			$this->l('No ID supplied', self::LOG_TYPE_ERROR);
			return;
		}

		$entrant_count = $this->arg('entrant-count');

		if(empty($entrant_count)) {
			$entrant_count = self::ENTRANT_COUNT_DEFAULT;
		}

		$tournament = $this->tournament->getTournamentRacingByTournamentID($id);
		$qualified_list = $this->arg('qualified');

		if(empty($qualified_list)) {
			$qualified_list = $this->_generateQualifiedList($entrant_count);
		} else {
			$qualified_list = $this->_extractQualifiedList($qualified_list);
		}

		$qualified_count = count($qualified_list);
		$this->l("Generated a qualified list with {$qualified_count} qualifiers");

		foreach($qualified_list as $qualified) {
			$this->l("{$qualified->rank}. {$qualified->name}");
		}

		$prize_pool 	= $this->_getPrizePoolValue($tournament, $entrant_count);
		$display_pool 	= '$' . number_format($prize_pool / 100, 2);

		$this->l("Prize pool value is {$display_pool}");
		$prize_list = $this->place->testPrizeDistribution($tournament, $qualified_list, $prize_pool);

		$this->l("Payout formula is {$prize_list['formula']}");
		$this->l("Prize list generated:");

		foreach($prize_list['place'] as $rank => $prize) {
			$current_prize = array();

			if(!empty($prize['ticket'])) {
				$current_prize[] = "Ticket to Tournament {$prize['ticket']}";
			}

			if(!empty($prize['cash'])) {
				$current_prize[] = '$' . number_format($prize['cash'] / 100, 2);
			}

			$prize_display = implode(' + ', $current_prize);
			$this->l("{$rank}. {$prize_display} x {$prize['count']}");
		}

		$this->l("How does that look?");
	}

	private function _getPrizePoolValue($tournament, $entrant_count) {
		$dynamic = $tournament->buy_in * $entrant_count;
		if($dynamic < $tournament->minimum_prize_pool) {
			return $tournament->minimum_prize_pool;
		}

		return $dynamic;
	}

	private function _generateQualifiedList($entrant_count) {
		$qualified_count 	= mt_rand(round($entrant_count / 2), $entrant_count);
		$rank 				= 1;
		$qualified_list 	= array();

		while($rank <= $qualified_count) {
			$at_rank = (mt_rand(1, 100) > 75) ? mt_rand(1, 5) : 1;

			for($i = 1; $i <= $at_rank; ++$i) {
				$qualified_list[] = $this->_newQualifier($rank);
			}

			$rank += $at_rank;
		}

		return $qualified_list;
	}

	private function _extractQualifiedList($qualified_list) {
		$rank_list = explode(',', $qualified_list);

		$extracted = array();
		foreach($rank_list as $rank) {
			$extracted[] = $this->_newQualifier($rank);
		}

		return $extracted;
	}

	private function _newQualifier($rank) {
		return (object)array(
			'rank' => $rank,
			'name' => md5(uniqid())
		);
	}
}

$utility = new TestPlace();
$utility->debug(true);
$utility->execute();